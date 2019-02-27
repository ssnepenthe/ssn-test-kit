<?php

namespace SsnTestKit;

/**
 * Adapted from illuminate/support.
 *
 * @see https://github.com/illuminate/support/blob/7547088cc18b51bf26fddbbeaf89b59e28d51956/helpers.php#L77-L98
 *
 * @return array<string, string>
 */
function class_uses_recursive(string $class) : array
{
    $results = [];

    foreach (array_reverse(class_parents($class) + [$class => $class]) as $class) {
        $results += trait_uses_recursive($class);
    }

    return array_unique($results);
}

/**
 * Adapted from illuminate/support.
 *
 * @see https://github.com/illuminate/support/blob/7547088cc18b51bf26fddbbeaf89b59e28d51956/helpers.php#L492-L509
 *
 * @return array<string, string>
 */
function trait_uses_recursive(string $trait) : array
{
    $traits = class_uses($trait);

    foreach ($traits as $trait) {
        $traits += trait_uses_recursive($trait);
    }

    return $traits;
}
