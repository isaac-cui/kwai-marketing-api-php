# Kwai Marketing API PHP SDK

快手海外 Marketing API 的非官方 PHP SDK。覆盖主手册与商务管理附属手册的 **61 个业务接口**，另支持 OAuth、分页、视频分片上传和资质上传。

要求：PHP 8.1+、64 位运行环境、ext-curl、ext-json。无第三方运行依赖。[完整接口索引](docs/endpoints.md) · [机器可读接口清单](docs/endpoints.json)

## 接入

源码目录可直接 `require '/path/to/kwai-marketing-api-php/autoload.php';`。使用 Composer 的项目可通过 GitHub VCS repository 引入当前库：

```json
{
  "repositories": [
    {"type": "vcs", "url": "https://github.com/isaac-cui/kwai-marketing-api-php"}
  ],
  "require": {"isaac-cui/kwai-marketing-api-php": "dev-main"}
}
```

配置好工作账号对该私有仓库的认证后执行 composer update，业务中加载 vendor/autoload.php。本库尚未发布到 Packagist；本地开发也可将上述 repository 改为 path 类型并指向源码目录。

```php
<?php
require __DIR__ . '/autoload.php';

use Kwai\Marketing\Client;

$client = new Client(getenv('KWAI_ACCESS_TOKEN') ?: '');
$response = $client->reports->accountEffect([
    'accountId' => 1,
    'dataBeginTime' => 1632873600000,
    'dataEndTime' => 1633046399999,
    'granularity' => 3,
    'timeZoneIana' => 'UTC+0',
    'pageNo' => 1,
    'pageSize' => 10,
    'withOutTax' => false,
]);
print_r($response->data());
```

替换账户和日期后设置 KWAI_ACCESS_TOKEN，可运行 [examples/report.php](examples/report.php)。

## 请求与响应

服务分组：reports、campaigns、units、creatives、materials、organic、business、finance。方法接收文档原名字段组成的关联数组。false、0、空数组和嵌套对象原样编码；根请求即使为空也发送 {}。新接口可用 `$client->call('/rest/n/mapi/...', $params)`。

HTTP 2xx 且业务 status=200 才成功。Response 的 data() 返回完整业务数据，requestId() 返回请求标识，envelope/raw 保留完整响应。失败抛出 ApiException：

```php
try {
    $response = $client->campaigns->list(['accountId' => 1, 'adCategory' => 1]);
} catch (\Kwai\Marketing\ApiException $e) {
    echo $e->httpStatus, ' ', $e->apiStatus, ' ', $e->requestId;
}
```

ApiException.responseBody 保留错误响应。64 位 PHP int 能准确表示手册中的 long ID；超过 PHP_INT_MAX 的响应数字通过 JSON_BIGINT_AS_STRING 保留为字符串。不要先将 ID 转换为 float，也不要对响应使用 JSON_NUMERIC_CHECK。请求需要发送数字 ID 时请使用范围内的 int。

## OAuth 和配置

```php
$token = $client->refreshAccessToken($clientId, $clientSecret, $refreshToken);
// 持久化返回的 refresh_token，并按 expires_in 提前刷新。
$client = $client->withAccessToken($token['access_token']);
```

手册中 access_token 约 1 小时，refresh_token 默认 360 天。刷新由调用方管理，不自动重放写入请求。withAccessToken 返回新客户端，原客户端不变。

```php
$client = new Client($token, [
    'timeout' => 30.0,
    'base_url' => 'https://developers.kwai.com',
    'token_url' => 'https://business.kwai.com/oauth/token',
]);
```

可注入 TransportInterface 进行测试或定制网络层。默认 cURL 验证 TLS、不跟随重定向，响应上限 32 MiB；网络错误使用 TransportException。单个业务调用不自动重试。

## 分页

```php
foreach ($client->pages('/rest/n/mapi/report/dspAccountEffectQuery', [
    'accountId' => 1, 'pageSize' => 100,
    // 补齐日期、时区等必填参数
], 50) as $page) {
    foreach ($page['data'] as $row) {
        // 消费一行；break 可终止迭代。
    }
}
```

仅适用于 data.total/data.data 响应；默认 pageNo=1/pageSize=20，不改动输入数组。达到总数或空页时结束，超过 maxPages 抛异常。中断后续请求应退出外层分页循环（嵌套循环可用 break 2）。

## 视频上传

1. materials->uploadToken() 传 accountId、fileType（1 竖版/2 横版）、fileFormat="mp4"。
2. 用响应 data.token 和 data.httpEndpointList 中的节点构造 Uploader。
3. `$uploader->uploadFile($path)` 按不超过 4 MiB 分片流式上传并完成。
4. materials->transferVideo() 传 accountId、token、fileType、name、footageLanguageTag、audioLanguageTag、aigc；首次调用发起转码。
5. 调用方按 500ms～1s 间隔并设置总截止时间查询，直到 step=4 且 checkFailedList 为空。校验失败时停止。

```php
$upload = $client->materials->uploadToken([
    'accountId' => 1, 'fileType' => 1, 'fileFormat' => 'mp4',
])->data();
$uploader = new \Kwai\Marketing\Uploader($upload['httpEndpointList'][0], $upload['token']);
$uploader->uploadFile('/absolute/path/video.mp4');
```

也提供 fragment()、complete()、resume() 供断点管理。上传 token 只能用于单个文件。裸节点域名使用 HTTPS；若节点仅支持手册中的 HTTP 可传显式 URL。上传服务只携带 upload_token。

## 资质与商务

business->licenseUploadToken() 获取资质 token；通过 Uploader->license($bytes) 将 JPG/JPEG/PNG 原始字节发送到 /api/upload，再调用 business->licenseFileKey() 获取 fileBlobKey，作为开户的 blobstoreKey。

business->createAccount()/account() 提供开户和查询；finance->charge()/refund() 提供充值和退款请求。资金对象使用整数 units/micros，例如 10.5 为 ['units' => 10, 'micros' => 500000]，不使用浮点金额自动换算。字段、权限和其他接口单位请按手册传入。

## 验证与已知边界

```sh
php tests/run.php
composer validate --no-check-publish
```

测试无需真实凭据或第三方测试库，覆盖模拟请求、错误、OAuth、分页、长整数、文件分片，以及本地 HTTP 服务上的真实 cURL 请求与重定向行为。

应用查询在手册中存在路径冲突，应用新建已暂停；PWA 等部分章节缺少明确方法标注。具体差异见接口索引。请求参数未全量建模，也未在本地重复服务端的枚举、预算或出价关联校验。当前未进行真实账号联调，实际权限、文档冲突与转码结果需在目标账号确认。

