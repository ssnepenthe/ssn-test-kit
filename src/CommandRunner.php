<?php

namespace SsnTestKit;

interface CommandRunner
{
    public function run(Command $command) : Command;
}
