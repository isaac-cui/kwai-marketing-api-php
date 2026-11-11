<?php
declare(strict_types=1);
namespace Kwai\Marketing;

final class ApiException extends \RuntimeException
{
    public function __construct(
        public readonly int $httpStatus,
        public readonly int $apiStatus,
        string $message,
        public readonly string $requestId = '',
        public readonly string $responseBody = '',
    ) {
        parent::__construct("Kwai HTTP {$httpStatus}, status {$apiStatus}, requestId {$requestId}: {$message}", $apiStatus);
    }
}
