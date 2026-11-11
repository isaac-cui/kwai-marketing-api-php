<?php
declare(strict_types=1);
namespace Kwai\Marketing;

final class FinanceService
{
    /** 广告主账户充值. @param array<string,mixed> $params */
    public function charge(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/finance/chargeFinance', $params);
    }

    /** 广告主账户退款. @param array<string,mixed> $params */
    public function refund(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/finance/refundMoney', $params);
    }
    public function __construct(private readonly Client $client) {}

    /** 广告主余额. @param array<string,mixed> $params */
    public function accountBalance(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/finance/financeAccountBalanceQuery', $params);
    }

}

