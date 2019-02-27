<?php

namespace SsnTestKit;

trait ManagesPosts
{
    protected function createPost(string $title, string $content, string $status = 'publish') : string
    {
        return $this->wp(sprintf(
            'post create --post_title=%s --post_content=%s --post_status=%s --porcelain',
            escapeshellarg($title),
            escapeshellarg($content),
            escapeshellarg($status)
        ));
    }

    // count, post_type, post_status, post_Title, post_author, post_date, post_date_gmt, post_content, max_depth, format
    protected function generatePosts(int $count = 1, array $args = []) : array
    {
        // @todo $args ignored for now, but this is where you would set post_type, post_status, etc.
        $postIds = $this->wp(sprintf(
            'post generate --count=%s --format=ids',
            escapeshellarg($count)
        ));

        return array_map('intval', explode(' ', $postIds));
    }
}
