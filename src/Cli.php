<?php

namespace SsnTestKit;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ExceptionInterface;

class Cli
{
    const MUST_RUN = true;

    protected $alias;
    protected $wpBinPath;

    public function process(string $command)
    {
        return Process::fromShellCommandline($command);
    }

    public function run(string $command, bool $mustRun = false)
    {
        $process = $this->process($command);

        if ($mustRun) {
            $process->mustRun();
        } else {
            $process->run();
        }

        return $process;
    }

    public function wp(string $command, bool $mustRun = false)
    {
        return $this->run($this->buildWpCommand($command), $mustRun);
    }

    public function runForOutput(string $command)
    {
        return trim($this->run($command, self::MUST_RUN)->getOutput());
    }

    public function wpForOutput(string $command)
    {
        return trim($this->wp($command, self::MUST_RUN)->getOutput());
    }

    public function buildWpCommand(string $command)
    {
        if (null !== $this->alias) {
            return sprintf(
                '%s %s %s',
                escapeshellarg($this->getWpBinPath()),
                escapeshellarg($this->alias),
                $command
            );
        }

        return sprintf('%s %s', escapeshellarg($this->getWpBinPath()), $command);
    }

    public function getWpBinPath()
    {
        if (null === $this->wpBinPath) {
            try {
                // Obviously not portable... May revisit at a later date.
                $wp = $this->runForOutput('which wp');
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

    public function setWpBinPath(string $path)
    {
        $this->wpBinPath = $path;

        return $this;
    }
}
