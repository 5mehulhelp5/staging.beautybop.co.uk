<?php

declare(strict_types=1);

namespace BeautyBop\Payments\Service;

use BeautyBop\Payments\Model\AccessToken;
use BeautyBop\Payments\Service\CredentialsProvider;
use RuntimeException;

class PayPalAuthenticator
{
    /**
     * @var HttpClient
     */
    private HttpClient $httpClient;

    /**
     * @var CredentialsProvider
     */
    private CredentialsProvider $credentialsProvider;

    public function __construct(
        HttpClient $httpClient,
        CredentialsProvider $credentialsProvider
    ) {
        $this->httpClient = $httpClient;
        $this->credentialsProvider = $credentialsProvider;
    }

    /**
     * Authenticate with PayPal.
     *
     * @throws RuntimeException
     */
    public function authenticate(): AccessToken
    {
        $credentials = $this->credentialsProvider->get();

         $authorization = base64_encode(
            $credentials->getClientId()
            . ':'
            . $credentials->getSecret()
        );

        $response = $this->httpClient->postForm(
            $credentials->getBaseUrl() . '/v1/oauth2/token',
            [
                'grant_type' => 'client_credentials'
            ],
            [
                'Authorization' => 'Basic ' . $authorization,
                'Accept' => 'application/json'
            ]
        );

        if (!$response->isSuccessful()) {

            throw new RuntimeException(
                sprintf(
                    "Authentication failed.\n\nHTTP Status: %d\n\nResponse:\n%s",
                    $response->getStatusCode(),
                    $response->getBody()
                )
            );
        }

        $json = $response->getJson();

        if (!isset(
            $json['access_token'],
            $json['token_type'],
            $json['expires_in']
        )) {
            throw new RuntimeException(
                'Invalid authentication response received from PayPal.'
            );
        }

        return new AccessToken(
            $json['access_token'],
            $json['token_type'],
            (int)$json['expires_in']
        );
    }
}