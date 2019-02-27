<?php

namespace SsnTestKit;

trait ManagesOptions
{
    protected function getOption(string $key)
    {
        // @todo Handle the case where option is not set.
        return json_decode(
            $this->wp(sprintf('option get %s --format=json', escapeshellarg($key))),
            true
        );
    }

    protected function setOption(string $key, $value) : string
    {
        return $this->wp(sprintf(
            'option set %s %s --format=json',
            escapeshellarg($key),
            escapeshellarg(json_encode($value))
        ));
    }
}
