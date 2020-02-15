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
            ->addArgument('name', InputArgument::REQUIRED)
            ->addArgument('version', InputArgument::OPTIONAL)
            ->addOption('quiet', 'q', InputOption::VALUE_NONE, 'Do not output any message')
            ->addOption('master', 'm', InputOption::VALUE_NONE, 'Installs the latest "master" release')
            // ->addOption('beta', 'b', InputOption::VALUE_NONE, 'Installs the latest "beta" release')
            ->addOption('no-install', null, InputOption::VALUE_NONE, 'Disables jumping into the FusionCMS installation process directly');
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
        $directory = $name and $name !== '.' ? getcwd().'/'.$name : getcwd();
        $version   = $this->getVersion($input);
        $stability = $this->getStability($input);

        if ($version) {
            $output->writeln('<info>Downloading FusionCMS ('.$stability.'/'.str_replace('"', '', $version).')...</info>');
        } else {
            $output->writeln('<info>Downloading FusionCMS ('.$stability.'/latest)...</info>');
        }

        $composer = $this->findComposer();
        $commands = [
            $composer.' create-project fusioncms/fusioncms '.$directory.' '.$version.' --stability="'.$stability.'" --prefer-dist',
        ];

        if (! $input->getOption('no-install')) {
            $commands[] = 'cd '.$directory;
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

        if ($input->getOption('master')) {
            $version = 'master';
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

        // if ($input->getOption('beta')) {
        //     $stability = 'beta';
        // }

        if ($input->getOption('master')) {
            $stability = 'dev';
        }

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