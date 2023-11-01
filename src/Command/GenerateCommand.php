<?php

namespace Bolt\Extension\Celtic34fr\ContactCore\Command;

use Bolt\Extension\Celtic34fr\ContactCore\Service\IndexGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateCommand extends Command
{
    protected static $defaultName = 'tnt-search:generate';
    private IndexGenerator $generator;

    /**
     * @param IndexGenerator $generator
     */
    public function init(IndexGenerator $generator): void
    {
        $this->generator = $generator;
    }

    protected function configure(): void
    {
        $this->setDescription('Generate the index for searching content.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->text('// Generating index');

        try {
            $this->generator->generate();
            $io->success('Index generated');
        } catch (\Throwable $t) {
            $io->error('Generating index failed');
            throw $t;
        }

        return Command::SUCCESS;
    }
}