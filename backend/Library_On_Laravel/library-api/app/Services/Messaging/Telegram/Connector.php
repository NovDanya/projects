<?php

namespace App\Services\Messaging\Telegram;

use GuzzleHttp\Client;

class Connector
{
    protected Client $httpClient;

    public function __construct()
    {
        $token = config('telegram.bot_token');
        $baseUri = config('telegram.api_base_uri');

        $this->httpClient = new Client([
            'base_uri' => $baseUri . $token . '/',
            'timeout'  => config('telegram.timeout'),
        ]);
    }

    protected function request(string $method, array $params): array
    {
        $response = $this->httpClient->post($method, [
            'form_params' => $params,
        ]);

        $data = json_decode($response->getBody(), true);

        if (!($data['ok'] ?? false)) {
            $errorDescription = $data['description'] ?? 'Unknown Telegram error';
            throw new \Exception("Telegram API error: {$errorDescription}");
        }

        return $data['result'] ?? [];
    }
}
