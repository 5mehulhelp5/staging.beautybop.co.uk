<?php

declare(strict_types=1);

namespace BeautyBop\Payments\Console;

use BeautyBop\Payments\Service\PayPalOrderCreator;
use BeautyBop\Payments\Service\UrlProvider;
use BeautyBop\Payments\Model\CreateOrderRequest;
use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class TestCreateOrder extends Command
{
    /**
     * @var PayPalOrderCreator
     */
    private PayPalOrderCreator $orderCreator;

    /**
     * @var UrlProvider
     */
    private UrlProvider $urlProvider;

    public function __construct(
        PayPalOrderCreator $orderCreator,
        UrlProvider $urlProvider

    ) {
        parent::__construct();
        $this->urlProvider = $urlProvider;
        $this->orderCreator = $orderCreator;
    }

    protected function configure(): void
    {
        $this->setName('beautybop:payments:test-order');

        $this->setDescription(
            'Test creating a PayPal Sandbox Order.'
        );

        parent::configure();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {

        $output->writeln('');
        $output->writeln('<info>BeautyBop Payments</info>');
        $output->writeln('--------------------------------');
        $output->writeln('');

        try {

            $output->writeln(
                '<comment>Creating PayPal Sandbox Order...</comment>'
            );

            $request = new CreateOrderRequest(
                'GBP',
                10.00,
                $this->urlProvider->getReturnUrl(),
                $this->urlProvider->getCancelUrl()
            );

            $order = $this->orderCreator->create($request);

            $output->writeln('');
            $output->writeln(
                '<info>✓ Order Created Successfully</info>'
            );

            $output->writeln('');

            $output->writeln(
                '<info>Order ID:</info>'
            );
            $output->writeln(
                $order->getId()
            );

            $output->writeln('');

            $output->writeln(
                '<info>Status:</info>'
            );
            $output->writeln(
                $order->getStatus()
            );

            $output->writeln('');

            $output->writeln(
                '<info>Approval URL:</info>'
            );
            $output->writeln(
                $order->getApproveUrl()
            );

            $output->writeln('');

            return Cli::RETURN_SUCCESS;

        } catch (Throwable $exception) {

            $output->writeln('');
            $output->writeln(
                '<error>✗ Failed to create PayPal Order</error>'
            );

            $output->writeln('');
            $output->writeln(
                $exception->getMessage()
            );

            return Cli::RETURN_FAILURE;
        }
    }
}