<?php

namespace SsnTestKit;

trait ManagesUserSessions
{
    protected function login() : void
    {
        // @todo Allow subclasses to configure credentials.
        $this->browser()->post('/wp-login.php', [
            'log' => 'admin',
            'pwd' => 'password',
        ]);
    }

    protected function logout() : void
    {
        // @todo Would it be better to specifically target WP cookies? Probably...
        $this->browser()->deleteAllCookies();
    }
}
