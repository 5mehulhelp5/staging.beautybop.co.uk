<?php

declare(strict_types=1);

namespace BeautyFort\BeautyfortProductImport\Console;

use BeautyFort\BeautyfortProductImport\Model\WebsiteLogin;
use BeautyFort\BeautyfortProductImport\Model\WebsiteSearch;
use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WebsiteLoginCommand extends Command
{
    /**
     * @var WebsiteLogin
     */
    private $websiteLogin;

    /**
     * @var WebsiteSearch
     */

    private  $websiteSearch;

    public function __construct(
        WebsiteLogin $websiteLogin,
        WebsiteSearch $websiteSearch,
        string $name = null
    ) {
        parent::__construct($name);

        $this->websiteLogin = $websiteLogin;
        $this->websiteSearch = $websiteSearch;
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

        if (!$success) {
            $output->writeln('<error>Login failed.</error>');
            return Command::FAILURE;
        }

        $output->writeln('<info>✓ Login successful</info>');
        $output->writeln('');

        $output->writeln('Searching for SKU K230265...');

        $previewId = $this->websiteSearch->findPreviewId('K230265');

        if ($previewId) {
            $output->writeln('<info>Preview ID: ' . $previewId . '</info>');
        } else {
            $output->writeln('<error>Preview ID not found.</error>');
        }

        return Command::SUCCESS;

    }
}