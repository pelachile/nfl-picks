<?php

namespace App\Http\Integrations\ESPN;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class ESPNConnector extends Connector
{
    use AcceptsJson;

    protected string $baseUrl = 'https://sports.core.api.espn.com/v2/sports/football/leagues/nfl';

    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Default headers for every request
     */
    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }
}
