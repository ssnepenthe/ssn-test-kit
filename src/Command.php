<?php

namespace SsnTestKit;

class Command
{
    protected $arguments = [];
    protected $command;
    protected $flags = [];
    protected $options = [];
    protected $runner;
    protected $subCommands = [];

    public function __construct($command)
    {
        $this->command = $command;
    }

    public static function make($command)
    {
        return new static($command);
    }

    public function addSubCommand($subCommand)
    {
        $this->subCommands[] = $subCommand;

        return $this;
    }

    public function addArgument($argument)
    {
        $this->arguments[] = $argument;

        return $this;
    }

    public function setOption($key, $value)
    {
        if ('-' !== $key[0]) {
            $key = "--{$key}";
        }

        $this->options[$key] = $value;

        return $this;
    }

    public function setFlag($flag)
    {
        if ('-' !== $flag[0]) {
            $flag = "--{$flag}";
        }

        $this->flags[$flag] = true;

        return $this;
    }

    public function setRunner(CommandRunner $runner)
    {
        $this->runner = $runner;

        return $this;
    }

    public function run()
    {
        if (null === $this->runner) {
            throw new \RuntimeException('Cannot run command until runner has been configured');
        }

        return $this->runner->run($this);
    }

    public function toArray()
    {
        $options = array_map(function ($value, $key) {
            return sprintf('%s=%s', escapeshellarg($key), escapeshellarg($value));
        }, $this->options, array_keys($this->options));

        $flags = array_map('escapeshellarg', array_keys($this->flags));

        return array_merge(
            [escapeshellarg($this->command)],
            array_map('escapeshellarg', $this->subCommands),
            array_map('escapeshellarg', $this->arguments),
            $options,
            $flags
        );
    }

    public function __toString()
    {
        return implode(' ', $this->toArray());
    }
}
