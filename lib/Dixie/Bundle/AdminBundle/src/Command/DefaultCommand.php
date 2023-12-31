<?php

declare(strict_types=1);

namespace Talav\AdminBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'talav:admin:default', description: 'Default Admin command.')]
class DefaultCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return Command::INVALID;
    }
}
