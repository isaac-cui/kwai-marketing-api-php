<?php
declare(strict_types=1);
require __DIR__ . '/../autoload.php';

use Kwai\Marketing\{ApiException, Client};

$client = new Client(getenv('KWAI_ACCESS_TOKEN') ?: '');
try {
    // Replace accountId and the millisecond date range before running.
    $response = $client->reports->accountEffect([
        'accountId' => 1, 'dataBeginTime' => 1632873600000, 'dataEndTime' => 1633046399999,
        'timeZoneIana' => 'UTC+0', 'granularity' => 3, 'pageNo' => 1, 'pageSize' => 10,
        'withOutTax' => false,
    ]);
    echo json_encode($response->data(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
} catch (ApiException $e) {
    fwrite(STDERR, 'API status: ' . $e->apiStatus . ', requestId: ' . $e->requestId . PHP_EOL);
    exit(1);
}
