<?php

namespace SsnTestKit\Tests;

use SsnTestKit\Cli;
use SsnTestKit\Command;
use PHPUnit\Framework\TestCase;

class CommandTest extends TestCase
{
    /** @test */
    public function verify_defaults()
    {
        $command = Command::make('ls');

        $this->assertEquals(Command::STATUS_PENDING, $command->getStatus());
        $this->assertNull($command->getExitCode());
        $this->assertNull($command->getOutput());
    }

    /** @test */
    public function it_can_handle_simple_commands()
    {
        $this->assertEquals("'ls'", (string) Command::make('ls'));
    }

    /** @test */
    public function it_can_handle_arbitrary_number_of_subcommands()
    {
        $this->assertEquals("'one' 'two'", (string) Command::make('one')->addSubCommand('two'));
        $this->assertEquals(
            "'one' 'two' 'three'",
            (string) Command::make('one')->addSubCommand('two')->addSubCommand('three')
        );
        $this->assertEquals(
            "'one' 'two' 'three' 'four'",
            (string) Command::make('one')
                ->addSubCommand('two')
                ->addSubCommand('three')
                ->addSubCommand('four')
        );
    }

    /** @test */
    public function it_can_handle_arbitrary_number_of_arguments()
    {
        $this->assertEquals("'one' 'two'", (string) Command::make('one')->addArgument('two'));
        $this->assertEquals(
            "'one' 'two' 'three'",
            (string) Command::make('one')->addArgument('two')->addArgument('three')
        );
        $this->assertEquals(
            "'one' 'two' 'three' 'four'",
            (string) Command::make('one')
                ->addArgument('two')
                ->addArgument('three')
                ->addArgument('four')
        );
    }

    /** @test */
    public function it_can_handle_commands_with_options()
    {
        $this->assertEquals("'cmd' '--a'='b'", (string) Command::make('cmd')->setOption('a', 'b'));
        $this->assertEquals("'cmd' '--a'='b'", (string) Command::make('cmd')->setOption('--a', 'b'));
        $this->assertEquals(
            "'cmd' '--a'='b' '--c'='d'",
            (string) Command::make('cmd')
                ->setOption('--a', 'b')
                ->setOption('--c', 'd')
        );
    }

    /** @test */
    public function it_can_handle_commands_with_flags()
    {
        $this->assertEquals("'cmd' '--a'", (string) Command::make('cmd')->setFlag('a'));
        $this->assertEquals("'cmd' '--a'", (string) Command::make('cmd')->setFlag('--a'));

        $this->assertEquals("'cmd' '-a'", (string) Command::make('cmd')->setFlag('-a'));
    }

    /** @test */
    public function it_can_handle_running_itself()
    {
        $cli = new Cli();
        $cli->setDebug(true);

        $cmd = Command::make('cmd');
        $cmd->setRunner($cli);

        $this->assertEquals("'cmd'", $cmd->run());
    }

    /** @test */
    public function it_throws_when_attempting_to_run_without_runner()
    {
        $this->expectException(\RuntimeException::class);

        Command::make('cmd')->run();
    }

    /** @test */
    public function it_puts_command_string_together_in_expected_order()
    {
        $cmd = Command::make('cmd');

        // Set all arguments in reverse.
        $cmd->setFlag('a');
        $cmd->setFlag('b');

        $cmd->setOption('c', 'd');
        $cmd->setOption('e', 'f');

        $cmd->addArgument('g');
        $cmd->addArgument('h');

        $cmd->addSubCommand('i');
        $cmd->addSubCommand('j');

        // And ensure they come out in correct order anyway.
        $this->assertEquals(
            ["'cmd'", "'i'", "'j'", "'g'", "'h'", "'--c'='d'", "'--e'='f'", "'--a'", "'--b'"],
            $cmd->toArray()
        );

        $this->assertEquals("'cmd' 'i' 'j' 'g' 'h' '--c'='d' '--e'='f' '--a' '--b'", (string) $cmd);
    }
}
