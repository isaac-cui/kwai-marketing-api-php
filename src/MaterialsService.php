<?php
declare(strict_types=1);
namespace Kwai\Marketing;

final class MaterialsService
{
    public function __construct(private readonly Client $client) {}

    /** 应用查询（接口清单路径，详见文档差异说明）. @param array<string,mixed> $params */
    public function apps(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/material/dspMaterialAppQuery', $params);
    }

    /** Pixel 查询. @param array<string,mixed> $params */
    public function pixels(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/material/dspMaterialPixelQuery', $params);
    }

    /** 视频查询. @param array<string,mixed> $params */
    public function videos(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/material/dspMaterialVideoQuery', $params);
    }

    /** 获取单文件上传令牌. @param array<string,mixed> $params */
    public function uploadToken(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/material/dspMaterialFileUploadToken', $params);
    }

    /** 发起/查询视频转码. @param array<string,mixed> $params */
    public function transferVideo(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/material/dspMaterialVideoTransfer', $params);
    }

    /** 创建 PWA. @param array<string,mixed> $params */
    public function createPwa(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/material/createPWA', $params);
    }

    /** 查询 PWA. @param array<string,mixed> $params */
    public function pwas(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/material/pageQueryPWA', $params);
    }

    /** 重试 PWA. @param array<string,mixed> $params */
    public function refreshPwa(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/material/refreshPWA', $params);
    }

    /** 申诉 PWA. @param array<string,mixed> $params */
    public function appealPwa(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/material/appealPWA', $params);
    }

    /** 头像. @param array<string,mixed> $params */
    public function avatars(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/material/dspMaterialAvatarQuery', $params);
    }

    /** 人群包. @param array<string,mixed> $params */
    public function populations(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/material/dspMaterialPopulationQuery', $params);
    }

    /** 试玩素材. @param array<string,mixed> $params */
    public function playables(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/material/dspMaterialPlayableQuery', $params);
    }

    /** 互动样式. @param array<string,mixed> $params */
    public function interactives(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/material/dspMaterialInteractiveQuery', $params);
    }

}

