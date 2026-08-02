<?php

declare(strict_types=1);

namespace BeautyBop\Payments\Service;

use Magento\Framework\HTTP\Client\Curl;
use Psr\Log\LoggerInterface;
use BeautyBop\Payments\Model\Response;

class HttpClient
{
    /**
     * @var Curl
     */
    private Curl $curl;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    public function __construct(
        Curl $curl,
        LoggerInterface $logger
    ) {
        $this->curl = $curl;
        $this->logger = $logger;
    }

    /**
     * Send GET request.
     */
    public function get(
        string $url,
        array $headers = []
    ): Response {

        foreach ($headers as $key => $value) {
            $this->curl->addHeader($key, $value);
        }

        $this->curl->get($url);

       return new Response(
            $this->curl->getStatus(),
            $this->curl->getBody()
        );
    }

    /**
     * Send POST request.
     */
    public function post(
        string $url,
        array $payload = [],
        array $headers = []
    ): Response {

        foreach ($headers as $key => $value) {
            $this->curl->addHeader($key, $value);
        }

        if (!isset($headers['Content-Type'])) {
            $this->curl->addHeader(
                'Content-Type',
                'application/json'
            );
        }

        if (!isset($headers['Accept'])) {
            $this->curl->addHeader(
                'Accept',
                'application/json'
            );
        }

        $this->curl->post(
            $url,
            json_encode($payload)
        );

        return new Response(
             
            $this->curl->getStatus(),
            $this->curl->getBody()
        );
    }

    /**
     * Send a form POST request.
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     *
     * @return Response
     */
    public function postForm(
        string $url,
        array $data = [],
        array $headers = []
    ): Response {

        foreach ($headers as $key => $value) {
            $this->curl->addHeader(
                $key,
                $value
            );
        }

        $this->curl->post(
            $url,
            http_build_query($data)
        );

        return new Response(
            $this->curl->getStatus(),
            $this->curl->getBody()
        );
    }
}