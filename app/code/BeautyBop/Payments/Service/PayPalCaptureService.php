<?php

declare(strict_types=1);

namespace BeautyBop\Payments\Service;

use BeautyBop\Payments\Model\CaptureResult;
use RuntimeException;

class PayPalCaptureService
{
    /**
     * @var PayPalAuthenticator
     */
    private PayPalAuthenticator $authenticator;

    /**
     * @var HttpClient
     */
    private HttpClient $httpClient;

    /**
     * @var CredentialsProvider
     */
    private CredentialsProvider $credentialsProvider;

    public function __construct(
        PayPalAuthenticator $authenticator,
        HttpClient $httpClient,
        CredentialsProvider $credentialsProvider
    ) {
        $this->authenticator = $authenticator;
        $this->httpClient = $httpClient;
        $this->credentialsProvider = $credentialsProvider;
    }

    /**
     * Capture an approved PayPal Order.
     *
     * @throws RuntimeException
     */
    public function capture(
        string $orderId
    ): CaptureResult {

        $accessToken = $this->authenticator->authenticate();

        $credentials = $this->credentialsProvider->get();

       $response = $this->httpClient->post(
            $credentials->getBaseUrl()
            . '/v2/checkout/orders/'
            . $orderId
            . '/capture',
            null,
            [
                'Authorization' => 'Bearer ' . $accessToken->getToken(),
                'Accept' => 'application/json'
            ]
        );
        if (!$response->isSuccessful()) {

            throw new RuntimeException(
                sprintf(
                    "Capture failed.\n\nHTTP Status: %d\n\n%s",
                    $response->getStatusCode(),
                    $response->getBody()
                )
            );
        }

        $json = $response->getJson();

        $captureId = '';
        $payerEmail = '';

        if (
            isset(
                $json['purchase_units'][0]['payments']['captures'][0]['id']
            )
        ) {
            $captureId =
                $json['purchase_units'][0]['payments']['captures'][0]['id'];
        }

        if (
            isset(
                $json['payer']['email_address']
            )
        ) {
            $payerEmail =
                $json['payer']['email_address'];
        }

        return new CaptureResult(
            $json['id'] ?? '',
            $json['status'] ?? '',
            $captureId,
            $payerEmail
        );
    }
}