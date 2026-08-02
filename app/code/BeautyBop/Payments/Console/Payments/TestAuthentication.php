<?php

declare(strict_types=1);

namespace BeautyBop\Payments\Console\Payments;

use BeautyBop\Payments\Service\PayPalAuthenticator;
use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestAuthentication extends Command
{
    /**
     * @var PayPalAuthenticator
     */
    private PayPalAuthenticator $authenticator;

    public function __construct(
        PayPalAuthenticator $authenticator
    ) {
        $this->authenticator = $authenticator;

        parent::__construct();
    }

    /**
     * Configure command.
     */
    protected function configure(): void
    {
        $this->setName('beautybop:payments:test-auth')
            ->setDescription(
                'Test authentication with the PayPal Sandbox.'
            );

        parent::configure();
    }

    /**
     * Execute command.
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {

        $output->writeln('');
        $output->writeln('<info>BeautyBop Payments</info>');
        $output->writeln(str_repeat('-', 45));
        $output->writeln('PayPal Sandbox Authentication');
        $output->writeln('');

        try {

            $output->writeln('Connecting to PayPal...');

            $token = $this->authenticator->authenticate();

            $output->writeln('');
            $output->writeln('<info>✓ Authentication Successful</info>');
            $output->writeln('');

            $output->writeln(
                'Token Type : ' . $token->getType()
            );

            $output->writeln(
                'Expires In : ' . $token->getExpiresIn() . ' seconds'
            );

            $output->writeln('');

            $output->writeln('Access Token:');
            $output->writeln(
                $token->getToken()
            );

            return Cli::RETURN_SUCCESS;

        } catch (\Throwable $e) {

            $output->writeln('');
            $output->writeln(
                '<error>Authentication Failed</error>'
            );

            $output->writeln('');
            $output->writeln(
                $e->getMessage()
            );

            return Cli::RETURN_FAILURE;
        }
    }
}