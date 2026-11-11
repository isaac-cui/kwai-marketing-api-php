<?php
declare(strict_types=1);
namespace Kwai\Marketing;

final class BusinessService
{
    public function __construct(private readonly Client $client) {}

    /** 资质文件上传令牌. @param array<string,mixed> $params */
    public function licenseUploadToken(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/bc/bcLicenseFileUploadToken', $params);
    }

    /** 资质文件唯一标识. @param array<string,mixed> $params */
    public function licenseFileKey(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/bc/bcLicenseFileUploadTokenToFileKey', $params);
    }

    /** 新建广告主账户. @param array<string,mixed> $params */
    public function createAccount(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/bc/bcAdAccountCreate', $params);
    }

    /** 查询广告主账户. @param array<string,mixed> $params */
    public function account(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/bc/bcAccountQueryByAdAccount', $params);
    }

}

