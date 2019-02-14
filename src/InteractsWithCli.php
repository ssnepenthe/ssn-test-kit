<?php

namespace SsnTestKit;

trait InteractsWithCli
{
    protected static $cli;

    public function cli()
    {
        if (null === static::$cli) {
            static::$cli = new Cli();

            if (method_exists($this, 'wpCliBinPath')) {
                static::$cli->setWpBinPath($this->wpCliBinPath());
            } elseif (property_exists($this, 'wpCliBinPath')) {
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
