<?php
declare(strict_types=1);
namespace Kwai\Marketing;

final class ReportsService
{
    public function __construct(private readonly Client $client) {}

    /** 广告主消耗. @param array<string,mixed> $params */
    public function accountEffect(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/report/dspAccountEffectQuery', $params);
    }

    /** 代理商消耗. @param array<string,mixed> $params */
    public function agentEffect(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/report/crmAccountQuery', $params);
    }

    /** 代理商余额. @param array<string,mixed> $params */
    public function agentBalance(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/report/financeBalanceQuery', $params);
    }

    /** 代理商月度消耗. @param array<string,mixed> $params */
    public function agentMonthlyConsumption(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/report/agentMonthlyConsumptionReportQuery', $params);
    }

    /** 广告主月度消耗. @param array<string,mixed> $params */
    public function accountMonthlyConsumption(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/report/accountMonthlyConsumptionReportQuery', $params);
    }

    /** 广告主消耗明细. @param array<string,mixed> $params */
    public function accountMonthlyDetailConsumption(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/report/accountMonthlyDetailConsumptionReportQuery', $params);
    }

    /** 广告计划报表. @param array<string,mixed> $params */
    public function campaignEffect(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/report/dspCampaignEffectQuery', $params);
    }

    /** 广告组报表. @param array<string,mixed> $params */
    public function unitEffect(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/report/dspUnitEffectQuery', $params);
    }

    /** 广告创意报表. @param array<string,mixed> $params */
    public function creativeEffect(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/report/dspCreativeEffectQuery', $params);
    }

    /** 视频报表. @param array<string,mixed> $params */
    public function videoEffect(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/report/dspPhotoEffectQuery', $params);
    }

    /** App 分国家报表. @param array<string,mixed> $params */
    public function appTargetEffect(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/report/dspAppTargetEffectQuery', $params);
    }

    /** 投放地/操作系统报表. @param array<string,mixed> $params */
    public function populationEffect(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/report/dspPopulationAnalysisEffectQuery', $params);
    }

    /** 账户详情. @param array<string,mixed> $params */
    public function accountInfos(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/report/dspAccountInfosQuery', $params);
    }

    /** 账户列表. @param array<string,mixed> $params */
    public function accounts(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/report/crmAccountQueryByAgentOrCorp', $params);
    }

    /** 三方广告日报. @param array<string,mixed> $params */
    public function adxDaily(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/adx/getAdxReport/day', $params);
    }

}

