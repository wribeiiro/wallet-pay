<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Response;

class TransactionAuthorizeService
{
    public function __construct(
        private Client $client
    ) {
        $this->client = new Client(['base_uri' => 'https://run.mocky.io/']);
    }

    public function statusAuthorizeTransaction(): array
    {
        try {
            $authorizeResponse = $this->client->request('GET', 'v3/8fafdd68-a090-496f-8c9a-3442cf30dae6');
            return json_decode($authorizeResponse->getBody(), true);
        } catch (GuzzleException) {
            return [
                'message' => Response::$statusTexts[Response::HTTP_UNAUTHORIZED],
                'code' => Response::HTTP_UNAUTHORIZED
            ];
        }
    }
}