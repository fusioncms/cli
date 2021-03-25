<?php

namespace FusionCMS\CLI\Console;

use Symfony\Component\Process\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class MakeAddonCommand extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('make:addon')
            ->setDescription('Create a new FusionCMS addon')
            ->addArgument('namespace', InputArgument::REQUIRED, 'Addon namespace')
            ->addArgument('path', InputArgument::OPTIONAL, 'Installation path')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Overwrite existing files');
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
        $namespace = $input->getArgument('namespace');
        $path      = $input->getArgument('path') ?? getcwd();
        $force     = $input->getOption('force') ? true : false;

        $name = preg_split('/[^a-z]/i', $namespace);
        $name = end($name);
        $slug = slugify($namespace);

        $addonpath = rtrim("{$path}/{$slug}", "/") . "/";
        $stubpath  = __DIR__ . '/../stubs/addon/';

        $finder     = new Finder;
        $filesystem = new Filesystem;

        $filesystem->mkdir($addonpath);

        foreach ($finder->in($stubpath) as $file) {
            if ($file->isDir()) {
                $filesystem->mkdir($addonpath . $file->getRelativePathname());
            } else {
                $filesystem->copy(
                    $stubpath . $file->getRelativePathname(),
                    $addonpath . $file->getRelativePathname(),
                    $force
                );
            }
        }

        /**
         * Configure `composer.json`
         */
        replacePlaceholders("{$addonpath}composer.json", [
            '{name}'        => $name,
            '{slug}'        => $slug,
            '{namespace}'   => str_replace('\\', '\\\\', $namespace),
            '{description}' => 'Addon module for FusionCMS.',
        ]);

        /**
         * Rename some files for consistency..
         */
        $filesystem->rename(
            "{$addonpath}config/stub.php",
            "{$addonpath}config/{$slug}.php",
            true
        );

        $output->writeln('<info>Addon template created!</info>');

        return 0;
    }
}