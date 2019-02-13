<?php

namespace SsnTestKit;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ExceptionInterface;

class Cli implements CommandRunner
{
    protected $alias;
    protected $debug;
    protected $globalFlags = [];
    protected $globalOptions = [];
    protected $wpBinPath;

    // @todo Not certain what the API should look like long-term, let's keep it simple for now...
    public function run(Command $command) : Command
    {
        // Should (hopefully) simplify testing.
        if ($this->debug) {
            $command->setStatus(Command::STATUS_DEBUG);
            $command->setExitCode(0);
            $command->setOutput('');
        } else {
            $process = Process::fromShellCommandline((string) $command);

            $process->run();

            $command->setStatus(Command::STATUS_COMPLETE);
            $command->setExitCode($process->getExitCode());
            $command->setOutput($process->getOutput());
        }

        return $command;
    }

    public function wp()
    {
        $command = Command::make($this->getWpBinPath());

        if (null !== $this->alias) {
            // Maybe not appropriate to treat alias as subcommand, but it does the trick for now...
            $command->addSubCommand($this->alias);
        }

        // Here instead of ->run() to ensure local options override global options.
        foreach ($this->globalFlags as $flag => $_) {
            $command->setFlag($flag);
        }

        foreach ($this->globalOptions as $key => $value) {
            $command->setOption($key, $value);
        }

        $command->setRunner($this);

        return $command;
    }

    public function getWpBinPath()
    {
        if (null === $this->wpBinPath) {
            try {
                // Obviously not portable... May revisit at a later date.
                $wp = trim((new Process(['which', 'wp']))->mustRun()->getOutput());
            } catch (ExceptionInterface $e) {
                if ($this->debug) {
                    // @todo Just let it throw?
                    $wp = 'wp';
                } else {
                    throw $e;
                }
            }

            $this->wpBinPath = $wp;
        }

        return $this->wpBinPath;
    }

    public function setAlias(string $alias)
    {
        if ('@' !== $alias[0]) {
            $alias = "@{$alias}";
        }

        $this->alias = $alias;

        return $this;
    }

    public function setDebug(bool $debug)
    {
        $this->debug = $debug;

        return $this;
    }

    public function setGlobalFlag(string $flag)
    {
        $this->globalFlags[$flag] = true;

        return $this;
    }

    public function setGlobalFlags(array $flags)
    {
        $this->globalFlags = [];

        foreach ($flags as $flag) {
            $this->setGlobalFlag($flag);
        }

        return $this;
    }

    public function setGlobalOption(string $key, string $value)
    {
        $this->globalOptions[$key] = $value;

        return $this;
    }

    public function setGlobalOptions(array $options)
    {
        $this->globalOptions = [];

        foreach ($options as $key => $value) {
            $this->setGlobalOption($key, $value);
        }

        return $this;
    }

    public function setWpBinPath(string $path)
    {
        $this->wpBinPath = $path;

        return $this;
    }
}
