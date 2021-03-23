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

        $slug = 
        $addonpath = rtrim("{$path}/{$namespace}", "/") . "/";
        $stubpath  = __DIR__ . '/stubs/addon';

        $finder     = new Finder;
        $filesystem = new Filesystem;

        $filesystem->mkdir($addonpath);

        foreach ($finder->in($stubpath) as $file) {
            if ($file->isDir()) {
                $filesystem->mkdir(
                    $addonpath . str_replace($stubpath, '', $file->getRealPath())
                );
            } else {            
                $filesystem->copy(
                    $file->getRealPath(),
                    $addonpath . str_replace($stubpath, '', $file->getRealPath()),
                    $force
                );
            }
        }

        $this->replacePlaceholders("{$addonpath}composer.json", [
            '{name}'        => ($name = basename($namespace)),
            '{slug}'        => ($slug = slugify($name)),
            '{namespace}'   => $namespace,
            '{description}' => 'Addon module for FusionCMS.',
        ]);

        $output->writeln('<info>Addon template created!</info>');

        return 0;
    }

    /**
     * Target file path and run string replacement method.
     * 
     * @param  string $path
     * @param  array  $replacements
     * @return void
     */
    private function replacePlaceholders($path, $replacements = [])
    {
        if (file_exists($path)) {
            file_put_contents(
                $path,
                strtr(
                    file_get_contents($path),
                    $replacements
                )
            );
        }
    }
}