<?php

namespace SsnTestKit;

trait InteractsWithCli
{
    /**
     * @var Cli|null
     */
    protected static $cli;

    /**
     * @return Cli
     *
     * @psalm-suppress InvalidNullableReturnType
     */
    public function cli() : Cli
    {
        if (null === static::$cli) {
            static::$cli = new Cli();

            if (method_exists($this, 'wpCliBinPath')) {
                static::$cli->setWpBinPath($this->wpCliBinPath());
            } elseif (property_exists($this, 'wpCliBinPath')) {
                /**
                 * @psalm-suppress UndefinedThisPropertyFetch
                 */
                static::$cli->setWpBinPath($this->wpCliBinPath);
            }

            if (method_exists($this, 'wpCliAlias')) {
                static::$cli->setAlias($this->wpCliAlias());
            } elseif (property_exists($this, 'wpCliAlias')) {
                static::$cli->setAlias($this->wpCliAlias);
            }
        }

        return static::$cli;
    }
}
