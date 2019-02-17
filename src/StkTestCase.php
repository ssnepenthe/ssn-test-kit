<?php

namespace SsnTestKit;

use PHPUnit\Framework\TestCase;

class StkTestCase extends TestCase
{
    use InteractsWithCli,
        MakesHttpRequests;

    public function setUp()
    {
        $uses = $this->classUsesRecursive(static::class);

        if (array_key_exists(ResetsSite::class, $uses)) {
            $this->resetSite();
        }

        if (array_key_exists(ManagesUserSessions::class, $uses)) {
            $this->logout();
        }
    }

    public static function tearDownAfterClass()
    {
        // $uses = class_uses(static::class);

        // if (array_key_exists(ResetsSite::class, $uses)) {
        //     // @todo Can't access in static context...
        //     $this->resetSite();
        // }

        if (static::$browser instanceof Browser) {
            static::$browser->quit();
        }
    }

    /**
     * Adapted from illuminate/support.
     *
     * @see https://github.com/illuminate/support/blob/7547088cc18b51bf26fddbbeaf89b59e28d51956/helpers.php#L77-L98
     *
     * @todo Move out of testcase - maybe to helpers file?
     */
    protected function classUsesRecursive(string $class)
    {
        $results = [];

        foreach (array_reverse(class_parents($class) + [$class => $class]) as $class) {
            $results += $this->traitUsesRecursive($class);
        }

        return array_unique($results);
    }

    /**
     * Adapted from illuminate/support.
     *
     * @see https://github.com/illuminate/support/blob/7547088cc18b51bf26fddbbeaf89b59e28d51956/helpers.php#L492-L509
     *
     * @todo Move out of testcase - maybe to helpers file?
     */
    protected function traitUsesRecursive(string $trait)
    {
        $traits = class_uses($trait);

        foreach ($traits as $trait) {
            $traits += $this->traitUsesRecursive($trait);
        }

        return $traits;
    }
}
