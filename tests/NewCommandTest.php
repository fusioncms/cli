<?php

namespace FusionCMS\CLI\Tests;

use FilesystemIterator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class NewCommandTest extends TestCase
{

	public function setUp(): void
    {
        parent::setUp();

        $this->filesystem = new Filesystem();
        $this->finder     = new Finder();

        $this->cleanDirectory(__DIR__.'/output');
    }

	/** @test */
	public function it_can_test()
	{
		$this->assertTrue(true);
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