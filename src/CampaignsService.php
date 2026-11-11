<?php
declare(strict_types=1);
namespace Kwai\Marketing;

final class CampaignsService
{
    public function __construct(private readonly Client $client) {}

    /** Campaign List. @param array<string,mixed> $params */
    public function list(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/campaign/dspCampaignPageQueryPerformance', $params);
    }

    /** Campaign Create. @param array<string,mixed> $params */
    public function create(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/campaign/dspCampaignAddPerformance', $params);
    }

    /** Campaign Update. @param array<string,mixed> $params */
    public function update(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/campaign/dspCampaignUpdatePerformance', $params);
    }

    /** Campaign UpdateStatus. @param array<string,mixed> $params */
    public function updateStatus(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/campaign/dspCampaignUpdateOpenStatusPerformance', $params);
    }

    /** Campaign Delete. @param array<string,mixed> $params */
    public function delete(array $params = []): Response
    {
        return $this->client->call('/rest/n/mapi/campaign/dspCampaignDeletePerformance', $params);
    }

}

