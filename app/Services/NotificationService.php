<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Response;

class NotificationService
{
    public function __construct(
        private Client $client
    ) {
        $this->client = new Client(['base_uri' => 'http://o4d9z.mocklab.io/']);
    }

    public function notify(): array
    {
        try {
            $notifyResponse = $this->client->request('GET', 'notify');
            return json_decode($notifyResponse->getBody(), true);
        } catch (GuzzleException) {
            return [
                'message' => Response::$statusTexts[Response::HTTP_SERVICE_UNAVAILABLE],
                'code' => Response::HTTP_SERVICE_UNAVAILABLE
            ];
        }
    }
}