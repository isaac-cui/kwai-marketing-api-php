<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';

use Kwai\Marketing\{ApiException, Client, CurlTransport, HttpResponse, Response, TransportInterface, Uploader};

final class MockTransport implements TransportInterface
{
    public array $calls = [];
    public array $responses = [];
    public function request(string $method, string $url, array $headers, string $body): HttpResponse
    {
        $this->calls[] = compact('method', 'url', 'headers', 'body');
        if ($this->responses === []) { throw new RuntimeException('Unexpected extra request'); }
        return array_shift($this->responses);
    }
}
function check(bool $condition, string $message): void {
    if (!$condition) { throw new RuntimeException($message); }
}
function throws(callable $fn, string $class): Throwable {
    try { $fn(); } catch (Throwable $error) {
        check($error instanceof $class, 'Unexpected exception: ' . $error::class . ' ' . $error->getMessage());
        return $error;
    }
    throw new RuntimeException('Expected exception ' . $class);
}
function ok(string $json = '{"status":200,"data":{}}'): HttpResponse { return new HttpResponse(200, $json); }

$tests = [];
$tests['request, field fidelity and large IDs'] = static function (): void {
    $mock = new MockTransport();
    $mock->responses[] = ok('{"status":200,"requestId":"r1","data":{"appId":1000000000000000304,"veryLarge":9223372036854775808}}');
    $client = new Client('test-token', [], $mock);
    $response = $client->reports->accountEffect([
        'accountId' => 1000000000000000304, 'withOutTax' => false, 'status' => 0,
        'unitIdList' => [], 'dataBeginTime' => 1632873600000,
    ]);
    $call = $mock->calls[0];
    check($call['method'] === 'POST', 'Wrong method');
    check($call['url'] === Client::DEFAULT_BASE_URL . '/rest/n/mapi/report/dspAccountEffectQuery', 'Wrong route');
    check($call['headers']['Access-Token'] === 'test-token', 'Missing token');
    $body = json_decode($call['body'], true, 512, JSON_THROW_ON_ERROR);
    check($body['withOutTax'] === false && $body['status'] === 0 && $body['unitIdList'] === [], 'Lost optional zero values');
    check(str_contains($call['body'], '1000000000000000304'), 'Request lost integer precision');
    check($response->data()['appId'] === 1000000000000000304, 'Lost large ID');
    check($response->data()['veryLarge'] === '9223372036854775808', 'Overflowing ID must remain a string');
    check($response->requestId() === 'r1' && $response->raw !== '', 'Missing metadata');
};
$tests['business, HTTP and malformed failures without retries'] = static function (): void {
    foreach ([
        [200, '{"status":401,"message":"Token is inValid","requestId":"trace"}', ApiException::class],
        [503, '<html>Unavailable</html>', ApiException::class],
        [200, '{', UnexpectedValueException::class],
        [200, '[]', UnexpectedValueException::class],
        [200, '{"data":{}}', ApiException::class],
    ] as [$status, $body, $class]) {
        $mock = new MockTransport();
        $mock->responses[] = new HttpResponse($status, $body);
        $client = new Client('token', [], $mock);
        $error = throws(fn() => $client->campaigns->create(['accountId' => 1]), $class);
        check(count($mock->calls) === 1, 'Mutation was retried');
        if (str_contains($body, 'trace')) {
            check($error->apiStatus === 401 && $error->requestId === 'trace', 'Missing error details');
        }
    }
};
$tests['OAuth encoding, rotation and token isolation'] = static function (): void {
    $mock = new MockTransport();
    $mock->responses = [ok('{"access_token":"new","refresh_token":"rotated","expires_in":3599}'), ok(), ok()];
    $client = new Client('old', [], $mock);
    $token = $client->refreshAccessToken('id', 'secret +&', 'refresh /?');
    parse_str(parse_url($mock->calls[0]['url'], PHP_URL_QUERY), $query);
    check($mock->calls[0]['method'] === 'GET' && $mock->calls[0]['headers'] === [], 'Wrong OAuth protocol');
    check($query['client_secret'] === 'secret +&' && $query['refresh_token'] === 'refresh /?', 'OAuth query encoding failed');
    check($token['refresh_token'] === 'rotated', 'Lost rotated token');
    $client->withAccessToken($token['access_token'])->reports->accounts();
    $client->reports->accounts();
    check($mock->calls[1]['headers']['Access-Token'] === 'new' && $mock->calls[2]['headers']['Access-Token'] === 'old', 'Token replacement mutated client');
};
$tests['pagination and bounded requests'] = static function (): void {
    $mock = new MockTransport();
    $mock->responses = [ok('{"status":200,"data":{"total":3,"data":[{"id":1},{"id":2}]}}'),
        ok('{"status":200,"data":{"total":3,"data":[{"id":3}]}}')];
    $client = new Client('token', [], $mock);
    $params = ['pageSize' => 2];
    $pages = iterator_to_array($client->pages('/rest/n/mapi/report/dspAccountEffectQuery', $params, 3));
    check(count($pages) === 2 && !isset($params['pageNo']), 'Pagination failed or mutated params');
    check(json_decode($mock->calls[1]['body'], true)['pageNo'] === 2, 'Page number not advanced');
    $mock->responses[] = ok('{"status":200,"data":{"total":100,"data":[{"id":1}]}}');
    throws(fn() => iterator_to_array($client->pages('/rest/n/mapi/report/dspAccountEffectQuery', [], 1)), RuntimeException::class);
    $mock->responses[] = ok('{"status":200,"data":{}}');
    throws(fn() => iterator_to_array($client->pages('/rest/n/mapi/report/dspAccountEffectQuery')), UnexpectedValueException::class);
};
$tests['streamed upload, resume and fragment boundaries'] = static function (): void {
    $mock = new MockTransport();
    $mock->responses = array_fill(0, 5, ok('{"result":1,"checksum":"check","size":3}'));
    $uploader = new Uploader('upload.example.test', 'upload +&', $mock);
    $file = tempnam(sys_get_temp_dir(), 'kwai-test-');
    try {
        file_put_contents($file, 'abcdefgh');
        $uploader->uploadFile($file, 3);
        $uploader->resume();
    } finally { unlink($file); }
    check(array_column(array_slice($mock->calls, 0, 3), 'body') === ['abc', 'def', 'gh'], 'Wrong binary chunks');
    foreach ($mock->calls as $call) {
        parse_str(parse_url($call['url'], PHP_URL_QUERY), $query);
        check($query['upload_token'] === 'upload +&' && !isset($call['headers']['Access-Token']), 'Wrong upload credentials');
    }
    parse_str(parse_url($mock->calls[3]['url'], PHP_URL_QUERY), $complete);
    check($complete['fragment_count'] === '3' && $mock->calls[4]['method'] === 'GET', 'Wrong complete/resume request');
    throws(fn() => $uploader->fragment(-1, 'x'), InvalidArgumentException::class);
    throws(fn() => $uploader->fragment(0, str_repeat('a', Uploader::MAX_FRAGMENT_SIZE + 1)), InvalidArgumentException::class);
    $mock->responses[] = ok('{"result":0}');
    throws(fn() => $uploader->fragment(0, 'x'), RuntimeException::class);
};
$tests['configuration and representative service paths'] = static function (): void {
    throws(fn() => new Client('x', ['base_url' => 'https://user:secret@example.test']), InvalidArgumentException::class);
    $mock = new MockTransport();
    $client = new Client('x', [], $mock);
    throws(fn() => $client->call('https://other.example'), InvalidArgumentException::class);
    foreach ([
        [fn() => $client->units->updateCboBid(), '/unit/dspUnitUpdateCboBidPerformance'],
        [fn() => $client->creatives->postAuditResult(), '/post/profilePostAuditResultPerformance'],
        [fn() => $client->materials->createPwa(), '/material/createPWA'],
        [fn() => $client->organic->postsV2(), '/organic/profilePostQueryV2'],
        [fn() => $client->finance->accountBalance(), '/finance/financeAccountBalanceQuery'],
    ] as [$fn, $suffix]) {
        $mock->responses[] = ok();
        $fn();
        check(str_ends_with($mock->calls[array_key_last($mock->calls)]['url'], $suffix), 'Wrong service route');
    }
};
$tests['real cURL transport and redirect isolation'] = static function (): void {
    $socket = stream_socket_server('tcp://127.0.0.1:0', $errno, $error);
    if ($socket === false) { throw new RuntimeException('Cannot reserve local test port'); }
    $address = stream_socket_get_name($socket, false);
    fclose($socket);
    $process = proc_open([PHP_BINARY, '-S', $address, __DIR__ . '/router.php'],
        [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, __DIR__, null, ['bypass_shell' => true]);
    if (!is_resource($process)) { throw new RuntimeException('Cannot start local HTTP test server'); }
    try {
        $ready = false;
        for ($i = 0; $i < 100; $i++) {
            $connection = @stream_socket_client('tcp://' . $address, $errno, $error, 0.1);
            if ($connection !== false) { fclose($connection); $ready = true; break; }
            usleep(20000);
        }
        check($ready, 'Test server did not start');
        $transport = new CurlTransport(2.0);
        $response = $transport->request('POST', 'http://' . $address . '/echo?q=a%2Bb',
            ['Access-Token' => 'local-test'], '{"flag":false}');
        $data = Response::decode($response->body);
        check($response->status === 200 && $data['method'] === 'POST' && $data['token'] === 'local-test'
            && $data['body'] === '{"flag":false}' && $data['query']['q'] === 'a+b', 'cURL request mismatch');
        $redirect = $transport->request('GET', 'http://' . $address . '/redirect', [], '');
        check($redirect->status === 307, 'cURL followed credential redirect');
    } finally {
        proc_terminate($process);
        foreach ($pipes as $pipe) { fclose($pipe); }
        proc_close($process);
    }
};

$tests['business, integer finance amounts and license compatibility'] = static function (): void {
    $mock = new MockTransport();
    $mock->responses = [ok('{"status":200,"data":{"token":"single-file"}}'), ok('{"status":500,"message":"declined"}'),
        ok('{"result":1}'), ok('{"status":1}')];
    $client = new Client('token', [], $mock);
    $client->withAccessToken('updated')->business->licenseUploadToken(['fileFormat' => 'png']);
    check(str_ends_with($mock->calls[0]['url'], '/bc/bcLicenseFileUploadToken'), 'Wrong business path');
    throws(fn() => $client->finance->charge(['accountId' => 1, 'chargeType' => 1,
        'chargeMoney' => ['units' => 10, 'micros' => 500000]]), ApiException::class);
    $body = json_decode($mock->calls[1]['body'], true, 512, JSON_THROW_ON_ERROR);
    check($body['chargeMoney'] === ['units' => 10, 'micros' => 500000], 'Money amount changed');
    check(count($mock->calls) === 2, 'Finance request retried');
    $uploader = new Uploader('upload.example.test', 'license', $mock);
    $uploader->license('png-content');
    $uploader->license('png-content');
    check(parse_url($mock->calls[2]['url'], PHP_URL_PATH) === '/api/upload', 'Wrong license upload route');
};

$failed = 0;
foreach ($tests as $name => $test) {
    try { $test(); echo "PASS: $name\n"; }
    catch (Throwable $error) { $failed++; fwrite(STDERR, "FAIL: $name: {$error->getMessage()}\n"); }
}
echo count($tests) . " tests, $failed failures\n";
exit($failed === 0 ? 0 : 1);
