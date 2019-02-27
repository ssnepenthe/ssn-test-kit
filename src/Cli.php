<?php

namespace SsnTestKit;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ExceptionInterface;

class Cli
{
    const MUST_RUN = true;

    /**
     * @var string|null
     */
    protected $alias;

    /**
     * @var string|null
     */
    protected $wpBinPath;

    public function run(string $command, bool $mustRun = false) : Process
    {
        $process = Process::fromShellCommandline($command);

        if ($mustRun) {
            $process->mustRun();
        } else {
            $process->run();
        }

        return $process;
    }

    public function wp(string $command, bool $mustRun = false) : Process
    {
        return $this->run($this->buildWpCommand($command), $mustRun);
    }

    public function buildWpCommand(string $command) : string
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

    public function getWpBinPath() : string
    {
        if (null === $this->wpBinPath) {
            try {
                // Obviously not portable... May revisit at a later date.
                $wp = trim($this->run('which wp', self::MUST_RUN)->getOutput());
            } catch (ExceptionInterface $e) {
                // @todo Just let it throw?
                $wp = 'wp';
            }

            $this->wpBinPath = $wp;
        }

        return $this->wpBinPath;
    }

    /**
     * @return self
     */
    public function setAlias(string $alias)
    {
        if ('@' !== $alias[0]) {
            $alias = "@{$alias}";
        }

        $this->alias = $alias;

        return $this;
    }

    /**
     * @return self
     */
    public function setWpBinPath(string $path)
    {
        $this->wpBinPath = $path;

        return $this;
    }
}
