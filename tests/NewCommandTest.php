<?php

namespace FusionCMS\CLI\Tests;

use FusionCMS\CLI\Console\NewCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class NewCommandTest extends TestCase
{
    public function tearDown(): void
    {
    	cleanDirectory(__DIR__.'/output');
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
}