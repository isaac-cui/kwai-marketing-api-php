<?php
declare(strict_types=1);
namespace Kwai\Marketing;

final class UnitsService
{
    public function __construct(private readonly Client $client) {}

    /** Unit List. @param array<string,mixed> $params */
    public function list(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/unit/dspUnitPageQueryPerformance', $params);
    }

    /** Unit Create. @param array<string,mixed> $params */
    public function create(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/unit/dspUnitAddPerformance', $params);
    }

    /** Unit Update. @param array<string,mixed> $params */
    public function update(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/unit/dspUnitUpdatePerformance', $params);
    }

    /** Unit UpdateStatus. @param array<string,mixed> $params */
    public function updateStatus(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/unit/dspUnitUpdateOpenStatusPerformance', $params);
    }

    /** Unit Delete. @param array<string,mixed> $params */
    public function delete(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/unit/dspUnitDeletePerformance', $params);
    }

    /** 广告组审核建议. @param array<string,mixed> $params */
    public function auditResult(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/unit/dspUnitAuditResultPerformance', $params);
    }

    /** 批量修改预算. @param array<string,mixed> $params */
    public function updateBudget(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/unit/dspUnitUpdateBudgetPerformance', $params);
    }

    /** 批量修改出价. @param array<string,mixed> $params */
    public function updateBid(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/unit/dspUnitUpdateBidPerformance', $params);
    }

    /** CBO 出价. @param array<string,mixed> $params */
    public function updateCboBid(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/unit/dspUnitUpdateCboBidPerformance', $params);
    }

    /** 衍生行为. @param array<string,mixed> $params */
    public function derivedActionTypes(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/unit/derivedActionType', $params);
    }

}

