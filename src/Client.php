<?php
declare(strict_types=1);
namespace Kwai\Marketing;

final class Client
{
    public const DEFAULT_BASE_URL = 'https://developers.kwai.com';
    public const DEFAULT_TOKEN_URL = 'https://business.kwai.com/oauth/token';
    private readonly TransportInterface $transport;
    private readonly string $baseUrl;
    private readonly string $tokenUrl;
    public readonly ReportsService $reports;
    public readonly CampaignsService $campaigns;
    public readonly UnitsService $units;
    public readonly CreativesService $creatives;
    public readonly MaterialsService $materials;
    public readonly OrganicService $organic;
    public readonly FinanceService $finance;
    public readonly BusinessService $business;

    /** @param array{base_url?:string,token_url?:string,timeout?:float} $options */
    public function __construct(
        private readonly string $accessToken = '',
        array $options = [],
        ?TransportInterface $transport = null,
    ) {
        $this->baseUrl = rtrim($options['base_url'] ?? self::DEFAULT_BASE_URL, '/');
        $this->tokenUrl = $options['token_url'] ?? self::DEFAULT_TOKEN_URL;
        self::validateUrl($this->baseUrl);
        self::validateUrl($this->tokenUrl);
        $this->transport = $transport ?? new CurlTransport($options['timeout'] ?? 30.0);
        $this->reports = new ReportsService($this);
        $this->campaigns = new CampaignsService($this);
        $this->units = new UnitsService($this);
        $this->creatives = new CreativesService($this);
        $this->materials = new MaterialsService($this);
        $this->organic = new OrganicService($this);
        $this->finance = new FinanceService($this);
        $this->business = new BusinessService($this);
    }

    public static function validateUrl(string $url): void
    {
        $parts = parse_url($url);
        if ($parts === false || !in_array($parts['scheme'] ?? '', ['http', 'https'], true)
            || empty($parts['host']) || isset($parts['user']) || isset($parts['pass'])
            || isset($parts['query']) || isset($parts['fragment'])) {
            throw new \InvalidArgumentException('Expected an HTTP(S) URL without credentials, query or fragment');
        }
    }

    public function withAccessToken(string $token): self
    {
        return new self($token, ['base_url' => $this->baseUrl, 'token_url' => $this->tokenUrl], $this->transport);
    }

    /** Performs one POST without automatic retries. @param array<string,mixed> $params */
    public function call(string $path, array $params = []): Response
    {
        if (!str_starts_with($path, '/rest/n/mapi/') || strpbrk($path, "?#\\") !== false || str_contains($path, '..')) {
            throw new \InvalidArgumentException('Expected an absolute /rest/n/mapi/ path');
        }
        if ($this->accessToken === '') {
            throw new \InvalidArgumentException('An access token is required');
        }
        $body = json_encode((object) $params, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $http = $this->transport->request('POST', $this->baseUrl . $path, [
            'Content-Type' => 'application/json', 'Access-Token' => $this->accessToken,
        ], $body);
        try {
            $envelope = Response::decode($http->body);
        } catch (\JsonException | \UnexpectedValueException $e) {
            if ($http->status >= 200 && $http->status < 300) {
                throw new \UnexpectedValueException('Invalid API response JSON', 0, $e);
            }
            throw new ApiException($http->status, 0, 'HTTP request failed', '', $http->body);
        }
        $status = $envelope['status'] ?? null;
        if ($http->status < 200 || $http->status >= 300 || $status !== 200) {
            throw new ApiException($http->status, is_int($status) ? $status : 0,
                (string) ($envelope['message'] ?? 'Missing or invalid API status'),
                (string) ($envelope['requestId'] ?? ''), $http->body);
        }
        return new Response($envelope, $http->body);
    }

    /** Explicit refresh; persist refresh_token and use withAccessToken afterwards.
     * @return array<string,mixed>
     */
    public function refreshAccessToken(string $clientId, string $clientSecret, string $refreshToken): array
    {
        if ($clientId === '' || $clientSecret === '' || $refreshToken === '') {
            throw new \InvalidArgumentException('Client ID, secret and refresh token are required');
        }
        $query = http_build_query([
            'grant_type' => 'refresh_token', 'client_id' => $clientId,
            'client_secret' => $clientSecret, 'refresh_token' => $refreshToken,
        ], '', '&', PHP_QUERY_RFC3986);
        $http = $this->transport->request('GET', $this->tokenUrl . '?' . $query, [], '');
        try { $token = Response::decode($http->body); }
        catch (\JsonException | \UnexpectedValueException) {
            throw new \UnexpectedValueException('Invalid OAuth response');
        }
        if ($http->status !== 200 || !is_string($token['access_token'] ?? null)
            || $token['access_token'] === '' || !is_int($token['expires_in'] ?? null) || $token['expires_in'] <= 0) {
            throw new \RuntimeException('OAuth refresh failed (HTTP ' . $http->status . ')');
        }
        return $token;
    }

    /** Standard data.total/data.data pagination. Parameters are copied.
     * @param array<string,mixed> $params
     * @return \Generator<int,array<string,mixed>>
     */
    public function pages(string $path, array $params = [], int $maxPages = 1000): \Generator
    {
        $page = $params['pageNo'] ?? 1;
        $size = $params['pageSize'] ?? 20;
        if (!is_int($page) || $page < 1 || !is_int($size) || $size < 1 || $maxPages < 1) {
            throw new \InvalidArgumentException('Positive integer pagination values are required');
        }
        $params['pageSize'] = $size;
        for ($i = 0; $i < $maxPages; $i++, $page++) {
            $params['pageNo'] = $page;
            $data = $this->call($path, $params)->data();
            if (!is_array($data) || !is_int($data['total'] ?? null) || $data['total'] < 0
                || !is_array($data['data'] ?? null) || !array_is_list($data['data'])) {
                throw new \UnexpectedValueException('Invalid pagination envelope');
            }
            yield $data;
            if ($data['data'] === [] || $page * $size >= $data['total']) { return; }
        }
        throw new \RuntimeException('Pagination reached maxPages before completion');
    }
}
