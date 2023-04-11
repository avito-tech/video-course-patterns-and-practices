<?php

declare(strict_types=1);

namespace App\Command;

use App\WarmUp;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class DependencyInjection extends Command
{
    protected static $defaultName = 'di:update';
    protected static $defaultDescription = 'Create or update dependency injection container.';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            WarmUp::warmUpDI(true);
        } catch (Throwable $e) {
            echo sprintf("%s.\r\n", $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
