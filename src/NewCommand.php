<?php

namespace FusionCMS\CLI\Console;

use Symfony\Component\Process\Process;
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
        $this
            ->setName('new')
            ->setDescription('Create a new FusionCMS instance')

            ->addArgument('name',    InputArgument::REQUIRED, 'Name of your new project.')
            ->addArgument('version', InputArgument::OPTIONAL, 'Requested version.', 'dev-nightly')
            ->addArgument('path',    InputArgument::OPTIONAL, 'Installation path')

            ->addOption('quiet',     'q',   InputOption::VALUE_NONE, 'Do not output any message')
            // ->addOption('nightly',   'm',   InputOption::VALUE_NONE, 'Installs the latest "nightly" release')
            ->addOption('install', null, InputOption::VALUE_NONE, 'Start the FusionCMS installer after downloading');
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
        $path      = $input->getArgument('path') ?? $name;
        $directory = $path and $path !== '.' ? getcwd().'/'.$path : getcwd();
        $version   = $input->getArgument('version');
        $stability = $this->getStability($input);

        if ($version) {
            $output->writeln('<info>Downloading FusionCMS ('.$stability.'/'.str_replace('"', '', $version).')...</info>');
        } else {
            $output->writeln('<info>Downloading FusionCMS ('.$stability.'/latest)...</info>');
        }

        $composer = $this->findComposer();
        $commands = [
            "{$composer} create-project fusioncms/fusioncms --stability=dev --remove-vcs {$directory}",
        ];

        if ($input->getOption('install')) {
            $commands[] = 'php artisan fusion:install';
        }

        if ($input->getOption('quiet')) {
            $commands = array_map(function($command) {
                return $command.' --quiet';
            }, $commands);
        }

        $process = Process::fromShellCommandline(implode(' && ', $commands));

        $process->setTimeout(0);
        $process->setTty(Process::isTtySupported());

        $process->run(function($type, $line) use ($output) {
            $output->write($line);
        });

        if ($process->isSuccessful()) {
            $output->writeln('<comment>FusionCMS ready! Build something amazing.</comment>');
        }

        return 0;
    }

    /**
     * Get the version that should be downloaded.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @return string
     */
    protected function getVersion(InputInterface $input)
    {
        $version = $input->getArgument('version');

        if ($input->getOption('nightly')) {
            $version = 'dev-nightly';
        }

        if ($version) {
            $version = '"'.$version.'"';
        }

        return $version;
    }

    /**
     * Get the stability constraint based on requested version.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @return string
     */
    protected function getStability(InputInterface $input)
    {
        $stability = 'beta';

        // if ($input->getOption('nightly')) {
        //     $stability = 'dev';
        // }

        return $stability;
    }

    /**
     * Get the composer command for the environment.
     *
     * @return string
     */
    protected function findComposer()
    {
        $composerPath = getcwd().'/composer.phar';

        if (file_exists($composerPath)) {
            return '"'.PHP_BINARY.'" '.$composerPath;
        }

        return 'composer';
    }
}