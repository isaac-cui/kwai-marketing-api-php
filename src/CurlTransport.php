<?php
declare(strict_types=1);
namespace Kwai\Marketing;

final class CurlTransport implements TransportInterface
{
    public function __construct(private readonly float $timeout = 30.0)
    {
        if (!is_finite($timeout) || $timeout <= 0) {
            throw new \InvalidArgumentException('Timeout must be positive');
        }
    }

    public function request(string $method, string $url, array $headers, string $body): HttpResponse
    {
        if (!extension_loaded('curl')) {
            throw new TransportException('The cURL extension is required');
        }
        $handle = curl_init($url);
        if ($handle === false) {
            throw new TransportException('Cannot initialize HTTP transport');
        }
        $lines = [];
        foreach ($headers as $name => $value) {
            if (strpbrk($name . $value, "\r\n") !== false) {
                curl_close($handle);
                throw new \InvalidArgumentException('Invalid HTTP header');
            }
            $lines[] = $name . ': ' . $value;
        }
        $responseBody = '';
        $tooLarge = false;
        curl_setopt_array($handle, [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $lines,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
            CURLOPT_CONNECTTIMEOUT_MS => min(10000, (int) ceil($this->timeout * 1000)),
            CURLOPT_TIMEOUT_MS => (int) ceil($this->timeout * 1000),
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_WRITEFUNCTION => static function ($curl, string $chunk) use (&$responseBody, &$tooLarge): int {
                if (strlen($responseBody) + strlen($chunk) > 32 * 1024 * 1024) {
                    $tooLarge = true;
                    return 0;
                }
                $responseBody .= $chunk;
                return strlen($chunk);
            },
        ]);
        if ($method !== 'GET') {
            curl_setopt($handle, CURLOPT_POSTFIELDS, $body);
        }
        try {
            if (curl_exec($handle) === false) {
                // cURL error messages/URLs may contain OAuth or upload credentials.
                throw new TransportException($tooLarge ? 'Response exceeds 32 MiB' : 'HTTP transport failed');
            }
            return new HttpResponse((int) curl_getinfo($handle, CURLINFO_HTTP_CODE), $responseBody);
        } finally {
            curl_close($handle);
        }
    }
}
