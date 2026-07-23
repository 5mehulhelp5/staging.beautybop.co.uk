<?php

declare(strict_types=1);

namespace BeautyFort\BeautyfortProductImport\Console;

use BeautyFort\BeautyfortProductImport\Model\WebsiteLogin;
use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WebsiteLoginCommand extends Command
{
    /**
     * @var WebsiteLogin
     */
    private WebsiteLogin $websiteLogin;

    public function __construct(
        WebsiteLogin $websiteLogin,
        string $name = null
    ) {
        parent::__construct($name);

        $this->websiteLogin = $websiteLogin;
    }

    protected function configure()
    {
        $this->setName('beautyfort:image:login');
        $this->setDescription('Test BeautyFort website login');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {

        $output->writeln('');
        $output->writeln('<info>========================================</info>');
        $output->writeln('<info> BeautyFort Website Login Test </info>');
        $output->writeln('<info>========================================</info>');
        $output->writeln('');

        $output->writeln('Attempting login...');

        $success = $this->websiteLogin->login();

        if ($success) {
            $output->writeln('');
            $output->writeln('<info>✓ Login successful</info>');
            $output->writeln('<info>Cookie saved to var/beautyfort.cookies</info>');

            return Cli::RETURN_SUCCESS;
        }

        $output->writeln('');
        $output->writeln('<error>✗ Login failed</error>');

        return Cli::RETURN_FAILURE;
    }
}