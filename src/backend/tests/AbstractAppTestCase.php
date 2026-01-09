<?php

namespace App\Tests;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use ApiPlatform\Api\IriConverterInterface;

abstract class AbstractAppTestCase extends ApiTestCase
{
    protected array $tokens = [];
    protected ?Client $client = null;

    public function getToken(string $email, string $password): string
    {
        if (empty($this->tokens[$email])) {
            $client = $this->getClient();

            // Authenticate and obtain a JWT token
            $client->request('POST', '/api/login_check', [
                'headers' => ['Content-Type' => 'application/json'],
                'json' => [
                    'username' => $email, // Replace with a valid username
                    'password' => $password, // Replace with a valid password
                ],
            ]);

            $responseData = json_decode($client->getResponse()->getContent(), true);
            $this->tokens[$email] = $responseData['token'];
        }

        return $this->tokens[$email];
    }

    protected function getHeaders(string $token): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => "Bearer $token"
        ];
    }

    protected function getClient(): Client
    {
        if ($this->client === null) {
            $this->client = static::createClient([], ['base_uri' => 'http://nginx/']);
        }
        return $this->client;
    }

    protected function getResourceByIri(string $iri): object
    {
        return static::getContainer()->get(IriConverterInterface::class)->getResourceFromIri($iri);
    }
}
