<?php

namespace FusionCMS\CLI\Tests;

use FusionCMS\CLI\Console\MakeAddonCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class MakeAddonCommandTest extends TestCase
{
    public function tearDown(): void
    {
    	cleanDirectory(__DIR__.'/output');
    }

	/** @test */
	public function it_can_make_addon_template()
	{
		$app = new Application('FusionCMS CLI');
        $app->add(new MakeAddonCommand);

        $command = new CommandTester($app->find('make:addon'));
        $status  = $command->execute([
        	'namespace' => 'Acme',
        	'path'      => __DIR__.'/output',
    		'--force'   => true
    	]);

        $this->assertSame(0, $status);
        $this->assertDirectoryExists(__DIR__.'/output/acme');
        $this->assertFileExists(__DIR__.'/output/acme/composer.json');
	}
}