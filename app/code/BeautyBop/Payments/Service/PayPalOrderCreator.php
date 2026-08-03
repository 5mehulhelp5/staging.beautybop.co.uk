<?php

declare(strict_types=1);

namespace BeautyBop\Payments\Service;

use BeautyBop\Payments\Model\Order;
use BeautyBop\Payments\Model\CreateOrderRequest;
use RuntimeException;

class PayPalOrderCreator
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
     * Create a PayPal Order.
     *
     * @throws RuntimeException
     */
    public function create(
            CreateOrderRequest $request
    ): Order {

        /*
         Authenticate with PayPal.
        */
        $accessToken = $this->authenticator->authenticate();

        /*
         Get PayPal credentials.
        */
        $credentials = $this->credentialsProvider->get();

        /*
         Build request payload.
        */
        $payload = [
            'intent' => 'CAPTURE',

            'payment_source' => [
                'paypal' => [
                    'experience_context' => [
                        'return_url' => $request->getReturnUrl(),
                        'cancel_url' => $request->getCancelUrl(),
                        'user_action' => 'PAY_NOW'
                    ]
                ]
            ],

            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => $request->getCurrency(),
                        'value' => number_format(
                            $request->getAmount(),
                            2,
                            '.',
                            ''
                        )
                    ]
                ]
            ]
        ];
        /*
         Create PayPal order.
        */
        $response = $this->httpClient->post(
            $credentials->getBaseUrl() . '/v2/checkout/orders',
            $payload,
            [
                'Authorization' => 'Bearer ' . $accessToken->getToken(),
                'Accept'        => 'application/json'
            ]
        );

        if (!$response->isSuccessful()) {

            throw new RuntimeException(
                sprintf(
                    "Unable to create PayPal order.\n\nHTTP Status: %d\n\nResponse:\n%s",
                    $response->getStatusCode(),
                    $response->getBody()
                )
            );
        }

        $json = $response->getJson();


        if (!isset(
            $json['id'],
            $json['status'],
            $json['links']
        )) {

            throw new RuntimeException(
                'Invalid PayPal order response.'
            );
        }

        /*
         Find approval URL.
        */
        $approveUrl = '';

        foreach ($json['links'] as $link) {

            if (
                in_array(
                    $link['rel'],
                    [
                        'approve',
                        'payer-action'
                    ],
                    true
                )
            ) {
                $approveUrl = $link['href'];
                break;
            }
        }

        return new Order(
            $json['id'],
            $json['status'],
            $approveUrl
        );
    }
}