<?php
declare(strict_types=1);
namespace Kwai\Marketing;

final class Uploader
{
    public const MAX_FRAGMENT_SIZE = 4 * 1024 * 1024;
    private readonly string $endpoint;
    private readonly TransportInterface $transport;

    /** Bare hosts use HTTPS. Pass explicit http:// only when required by your node. */
    public function __construct(string $endpoint, private readonly string $token, ?TransportInterface $transport = null)
    {
        if (!str_contains($endpoint, '://')) { $endpoint = 'https://' . $endpoint; }
        Client::validateUrl($endpoint);
        $parts = parse_url($endpoint);
        if (($parts['path'] ?? '') !== '' && $parts['path'] !== '/') {
            throw new \InvalidArgumentException('Upload endpoint must be an origin');
        }
        if ($token === '') { throw new \InvalidArgumentException('An upload token is required'); }
        $this->endpoint = rtrim($endpoint, '/');
        $this->transport = $transport ?? new CurlTransport();
    }

    /** @param array<string,int|string> $query @return array<string,mixed> */
    private function request(string $method, string $path, array $query, string $body = ''): array
    {
        $query['upload_token'] = $this->token;
        $url = $this->endpoint . $path . '?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986);
        $http = $this->transport->request($method, $url, ['Content-Type' => 'application/octet-stream'], $body);
        try { $result = Response::decode($http->body); }
        catch (\JsonException | \UnexpectedValueException) {
            throw new \UnexpectedValueException('Invalid upload response');
        }
        $code = $path === '/api/upload' ? ($result['result'] ?? $result['status'] ?? null) : ($result['result'] ?? null);
        if ($http->status !== 200 || $code !== 1) {
            throw new \RuntimeException('Upload service rejected request');
        }
        return $result;
    }

    /** @return array<string,mixed> */
    public function fragment(int $index, string $bytes): array
    {
        if ($index < 0 || $bytes === '' || strlen($bytes) > self::MAX_FRAGMENT_SIZE) {
            throw new \InvalidArgumentException('Nonnegative index and fragment size within 1 byte to 4 MiB required');
        }
        return $this->request('POST', '/api/upload/fragment', ['fragment_id' => $index], $bytes);
    }

    /** @return array<string,mixed> */
    public function complete(int $fragmentCount): array
    {
        if ($fragmentCount < 1) { throw new \InvalidArgumentException('Fragment count must be positive'); }
        return $this->request('POST', '/api/upload/complete', ['fragment_count' => $fragmentCount]);
    }

    /** @return array<string,mixed> */
    public function resume(): array { return $this->request('GET', '/api/upload/resume', []); }

    /** Raw JPG/JPEG/PNG bytes; use business->licenseFileKey() after uploading.
     * @return array<string,mixed>
     */
    public function license(string $bytes): array
    {
        if ($bytes === '') { throw new \InvalidArgumentException('Empty license file'); }
        return $this->request('POST', '/api/upload', [], $bytes);
    }

    /** Stream chunks, then complete. Transcoding requires materials->transferVideo().
     * @return array<string,mixed>
     */
    public function uploadFile(string $path, int $chunkSize = self::MAX_FRAGMENT_SIZE): array
    {
        if ($chunkSize < 1 || $chunkSize > self::MAX_FRAGMENT_SIZE) {
            throw new \InvalidArgumentException('Chunk size must be within 1 to 4 MiB');
        }
        $stream = @fopen($path, 'rb');
        if ($stream === false) { throw new \RuntimeException('Cannot open upload file'); }
        $count = 0;
        try {
            while (!feof($stream)) {
                $chunk = fread($stream, $chunkSize);
                if ($chunk === false) { throw new \RuntimeException('Cannot read upload file'); }
                if ($chunk === '') {
                    if (feof($stream)) { break; }
                    throw new \RuntimeException('Upload stream made no progress');
                }
                $this->fragment($count++, $chunk);
            }
            if ($count === 0) { throw new \InvalidArgumentException('Cannot upload an empty file'); }
            return $this->complete($count);
        } finally {
            fclose($stream);
        }
    }
}
