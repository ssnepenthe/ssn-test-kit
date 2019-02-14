<?php

namespace SsnTestKit\Tests;

use SsnTestKit\Cli;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

// @todo Not the most portable of tests...
class CliTest extends TestCase
{
    /** @test */
    public function it_can_run_a_command()
    {
        $cli = new Cli();
        $process = $cli->run('ls *.md');

        $this->assertInstanceOf(Process::class, $process);
        $this->assertStringContainsString('README.md', $process->getOutput());
    }

    /** @test */
    public function it_can_enforce_that_a_command_must_run()
    {
        $this->expectException(ProcessFailedException::class);

        $cli = new Cli();
        $process = $cli->run('not-a-real-command', Cli::MUST_RUN);
    }

    /** @test */
    public function it_can_run_a_wp_command()
    {
        $cli = new Cli();
        $process = $cli->wp('help');

        $this->assertStringContainsString('wp <command>', $process->getOutput());
    }

    /** @test */
    public function it_can_run_a_command_for_output()
    {
        $cli = new Cli();
        $output = $cli->runForOutput('ls *.md');

        $this->assertStringContainsString('README.md', $output);
    }

    /** @test */
    public function it_throws_if_there_is_an_error_when_running_for_output()
    {
        $this->expectException(ProcessFailedException::class);

        $cli = new Cli();
        $output = $cli->runForOutput('not-a-real-command');
    }

    /** @test */
    public function it_can_run_a_wp_command_for_output()
    {
        $cli = new Cli();
        $output = $cli->wpForOutput('help');

        $this->assertStringContainsString('wp <command>', $output);
    }

    /** @test */
    public function it_can_prepend_wp_to_a_command()
    {
        $cli = new Cli();

        $this->assertEquals(
            "'/Users/ryan/.composer/vendor/bin/wp' post create",
            $cli->buildWpCommand('post create')
        );

        $cli->setWpBinPath('wp');

        $this->assertEquals("'wp' post create", $cli->buildWpCommand('post create'));
    }

    /** @test */
    public function it_can_insert_alias_into_wp_command_if_set()
    {
        $cli = new Cli();

        // Sanity.
        $this->assertEquals(
            "'/Users/ryan/.composer/vendor/bin/wp' post create",
            $cli->buildWpCommand('post create')
        );

        $cli->setAlias('vvv');

        $this->assertEquals(
            "'/Users/ryan/.composer/vendor/bin/wp' '@vvv' post create",
            $cli->buildWpCommand('post create')
        );
    }
}
