<?php
declare(strict_types=1);
namespace Kwai\Marketing;

final class OrganicService
{
    public function __construct(private readonly Client $client) {}

    /** 已授权原生账户. @param array<string,mixed> $params */
    public function accounts(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/organic/authKwaiAccountQuery', $params);
    }

    /** 原生作品（已弃用）. @param array<string,mixed> $params */
    public function posts(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/organic/profilePostQuery', $params);
    }

    /** 原生作品 V2. @param array<string,mixed> $params */
    public function postsV2(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/organic/profilePostQueryV2', $params);
    }

    /** 视频同步至原生账户. @param array<string,mixed> $params */
    public function pushVideo(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/organic/profileMaterialVideoPush', $params);
    }

}

