<?php

namespace FusionCMS\CLI\Console;

use ZipArchive;
use RuntimeException;
use GuzzleHttp\Client;
use Symfony\Component\Process\Process;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewCommand extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        // putenv('COMPOSER_HOME='.__DIR__.'/vendor/bin/composer');

        $this
            ->setName('new')
            ->setDescription('Create a new FusionCMS instance')
            ->addArgument('name', InputArgument::OPTIONAL)
            ->addOption('quiet', 'q', InputOption::VALUE_NONE, 'Do not output any message')
            ->addOption('master', 'm', InputOption::VALUE_NONE, 'Installs the latest "master" release')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Forces install even if the directory already exists');
    }

    /**
     * Execute the command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Input\OutputInterface  $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name      = $input->getArgument('name');
        $directory = $name;
        // $directory = $name and $name !== '.' ? getcwd().'/'.$name : getcwd();

        $output->writeln('<info>Crafting application...</info>');

        $composer = $this->findComposer();
        $commands = [
            $composer.' create-project fusioncms/fusioncms '.$name.' dev-master',
        ];

        if ($input->getOption('quiet')) {
            $commands = array_map(function($command) {
                return $command.' --quiet';
            }, $commands);
        }

        $process = Process::fromShellCommandline(implode(' && ', $commands), $directory, null, null, null);

        if ('\\' != DIRECTORY_SEPARATOR and file_exists('/dev/tty') and is_readable('/dev/tty')) {
            $process->setTty(true);
        }

        $process->run(function($type, $line) use ($output) {
            $output->write($line);
        });

        if ($process->isSuccessful()) {
            $output->writeln('<comment>FusionCMS ready! Build something amazing.</comment>');
        }

        return 0;
    }

    protected function findComposer()
    {
        $composerPath = getcwd().'/composer.phar';

        if (file_exists($composerPath)) {
            return '"'.PHP_BINARY.'" '.$composerPath;
        }

        return 'composer';
    }
}