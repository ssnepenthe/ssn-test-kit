<?php

namespace SsnTestKit\Tests;

use SsnTestKit\Cli;
use SsnTestKit\Command;
use PHPUnit\Framework\TestCase;

class CliTest extends TestCase
{
    protected function makeCli($wpBinPath = null)
    {
        $cli = (new Cli())->setDebug(true);

        if ($wpBinPath) {
            $cli->setWpBinPath($wpBinPath);
        }

        return $cli;
    }

    /** @test */
    public function it_can_run_a_command()
    {
        $command = Command::make('cmd');

        // Sanity.
        $this->assertEquals(Command::STATUS_PENDING, $command->getStatus());
        $this->assertNull($command->getExitCode());
        $this->assertNull($command->getOutput());

        // Command is returned directly.
        $this->assertEquals("'cmd'", (string) $this->makeCli()->run($command));

        // State is adjusted as expected.
        $this->assertEquals(Command::STATUS_DEBUG, $command->getStatus());
        $this->assertSame(0, $command->getExitCode());
        $this->assertSame('', $command->getOutput());
    }

    /** @test */
    public function it_can_create_a_base_wp_cli_command()
    {
        $cli = $this->makeCli('wp');

        // Plain.
        $this->assertEquals("'wp'", (string) $cli->wp());

        // With alias.
        $cli->setAlias('@one');
        $this->assertEquals("'wp' '@one'", (string) $cli->wp());

        // Runner is already set.
        $this->assertInstanceOf(Command::class, $cli->wp()->run());
    }

    /** @test */
    public function wp_command_instances_are_unique()
    {
        $cli = $this->makeCli();

        $this->assertNotSame($cli->wp(), $cli->wp());
    }

    /** @test */
    public function it_can_automatically_locate_wp_cli()
    {
        // Obviously not portable - will need to revisit if I want to get some sort of CI going...
        $this->assertEquals(
            '/Users/ryan/.composer/vendor/bin/wp',
            $this->makeCli()->getWpBinPath()
        );
    }

    /** @test */
    public function alias_can_be_set()
    {
        $cli = $this->makeCli('wp');

        $cli->setAlias('@one');

        $this->assertEquals("'wp' '@one'", (string) $cli->wp());

        // '@' is added automatically.
        $cli->setAlias('two');

        $this->assertEquals("'wp' '@two'", (string) $cli->wp());
    }

    /** @test */
    public function global_flags_can_be_set()
    {
        $cli = $this->makeCli('wp');
        $cli->setGlobalFlag('one');
        $cli->setGlobalFlag('two');

        $cmd = $cli->wp();

        $this->assertEquals("'wp' '--one' '--two'", $cli->run($cmd));

        $cli = $this->makeCli('wp');
        $cli->setGlobalFlags(['three', 'four']);

        $cmd = $cli->wp();

        $this->assertEquals("'wp' '--three' '--four'", $cli->run($cmd));
    }

    /** @test */
    public function global_options_can_be_set()
    {
        $cli = $this->makeCli('wp');
        $cli->setGlobalOption('a', 'b');
        $cli->setGlobalOption('c', 'd');

        $cmd = $cli->wp();

        $this->assertEquals("'wp' '--a'='b' '--c'='d'", $cli->run($cmd));

        $cli = $this->makeCli('wp');
        $cli->setGlobalOptions([
            'e' => 'f',
            'g' => 'h'
        ]);

        $cmd = $cli->wp();

        $this->assertEquals("'wp' '--e'='f' '--g'='h'", $cli->run($cmd));
    }

    /** @test */
    public function command_options_take_precedence_over_global_options()
    {
        // @todo This is backwards...
        $cli = $this->makeCli('wp');
        $cli->setGlobalOption('a', 'b');

        $cmd = $cli->wp();
        $cmd->setOption('a', 'c');

        $this->assertEquals("'wp' '--a'='c'", $cli->run($cmd));
    }

    /** @test */
    public function bin_path_can_be_set()
    {
        $cli = $this->makeCli();
        $cli->setWpBinPath('/just/another/path/string');

        $this->assertEquals('/just/another/path/string', $cli->getWpBinPath());
    }
}
