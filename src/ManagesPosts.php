<?php

namespace SsnTestKit;

trait ManagesPosts
{
    protected function createPost(string $title, string $content, string $status = 'publish')
    {
        return $this->cli()->wpForOutput(sprintf(
            'post create --post_title=%s --post_content=%s --post_status=%s --porcelain',
            escapeshellarg($title),
            escapeshellarg($content),
            escapeshellarg($status)
        ));
    }
}
