<?php

declare(strict_types=1);

namespace App\Test;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MailerClient
{
    private static HttpClientInterface $staticClient;

    public function __construct()
    {
        self::$staticClient = HttpClient::createForBaseUri(
            'http://mailer:8025',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ],
        );
    }

    public function clear(): void
    {
        self::$staticClient->request('DELETE', '/api/v1/messages');
    }

    public function hasEmailSentTo(string $to): bool
    {
        $response = self::$staticClient->request('GET', '/api/v2/search?kind=to&query=' . urlencode($to));

        return $response->toArray()['total'] > 0;
    }

    public function countEmailSentTo(string $to): int
    {
        $response = self::$staticClient->request('GET', '/api/v2/search?kind=to&query=' . urlencode($to));

        return (int) $response->toArray()['total'];
    }
}