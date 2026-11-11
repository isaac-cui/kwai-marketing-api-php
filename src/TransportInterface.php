<?php
declare(strict_types=1);
namespace Kwai\Marketing;

interface TransportInterface
{
    /** @param array<string,string> $headers */
    public function request(string $method, string $url, array $headers, string $body): HttpResponse;
}
