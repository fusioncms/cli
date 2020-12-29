<?php

namespace FusionCMS\CLI\Tests;

use FilesystemIterator;
use FusionCMS\CLI\Console\NewCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class NewCommandTest extends TestCase
{
	/**
	 * @var \Symfony\Component\Filesystem\Filesystem
	 */
	private $filesystem;

	/**
	 * @var \Symfony\Component\Finder\Finder
	 */
	private $finder;

	/**
	 * Hello.
	 * 
	 */
	public function setUp(): void
    {
        parent::setUp();

        $this->filesystem = new Filesystem();
        $this->finder     = new Finder();
    }

    /**
	 * Goodbye.
	 * 
	 */
    public function tearDown(): void
    {
    	$this->cleanDirectory(__DIR__.'/output');
    }

	/** @test */
	public function it_can_install_fusioncms()
	{
		$app = new Application('FusionCMS CLI');
        $app->add(new NewCommand);

        $command = new CommandTester($app->find('new'));
        $status  = $command->execute([
        	'name'    => 'FusionCMS',
        	'path'    => __DIR__.'/output/fusioncms',
    		'--quiet' => true
    	]);

        $this->assertSame(0, $status);
        $this->assertDirectoryExists(__DIR__.'/output/fusioncms/vendor/fusioncms');
	}

	/**
	 * Clean output directory for next test.
	 * 
	 * @access private
	 * @param  string $directory [description]
	 * @return void
	 */
	private function cleanDirectory($directory)
	{
		if (is_dir($directory)) {
			$files = $this->finder->in($directory)->depth('== 0');
			$paths = [];

			foreach ($files as $file) {
				$paths[] = $file->getPathname();
			}

			$this->filesystem->remove($paths);
		}
	}
}