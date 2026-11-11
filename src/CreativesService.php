<?php
declare(strict_types=1);
namespace Kwai\Marketing;

final class CreativesService
{
    public function __construct(private readonly Client $client) {}

    /** Creative List. @param array<string,mixed> $params */
    public function list(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/creative/dspCreativePageQueryPerformance', $params);
    }

    /** Creative Create. @param array<string,mixed> $params */
    public function create(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/creative/dspCreativeAddPerformance', $params);
    }

    /** Creative Update. @param array<string,mixed> $params */
    public function update(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/creative/dspCreativeUpdatePerformance', $params);
    }

    /** Creative UpdateStatus. @param array<string,mixed> $params */
    public function updateStatus(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/creative/dspCreativeUpdateOpenStatusPerformance', $params);
    }

    /** Creative Delete. @param array<string,mixed> $params */
    public function delete(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/creative/dspCreativeDeletePerformance', $params);
    }

    /** 创意审核建议. @param array<string,mixed> $params */
    public function auditResult(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/creative/dspCreativeAuditResultPerformance', $params);
    }

    /** 社区审核建议. @param array<string,mixed> $params */
    public function postAuditResult(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/post/profilePostAuditResultPerformance', $params);
    }

}

