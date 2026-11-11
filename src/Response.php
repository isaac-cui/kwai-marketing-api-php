<?php
declare(strict_types=1);
namespace Kwai\Marketing;

final class Response
{
    /** @param array<string,mixed> $envelope */
    public function __construct(public readonly array $envelope, public readonly string $raw) {}

    public function data(): mixed { return $this->envelope['data'] ?? null; }
    public function requestId(): string { return (string) ($this->envelope['requestId'] ?? ''); }

    /** @return array<string,mixed> */
    public static function decode(string $body): array
    {
        $value = json_decode($body, true, 512, JSON_THROW_ON_ERROR | JSON_BIGINT_AS_STRING);
        if (!is_array($value) || !str_starts_with(ltrim($body), '{')) {
            throw new \UnexpectedValueException('Expected a JSON object response');
        }
        return $value;
    }
}
