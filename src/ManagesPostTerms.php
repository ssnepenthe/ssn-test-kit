<?php

namespace SsnTestKit;

trait ManagesPostTerms
{
    protected function addTermsToPost(int $postId, string $taxonomy, int ...$termIds) : string
    {
        return $this->cli()->wpForOutput(sprintf(
            'post term add %s %s %s --by=id',
            escapeshellarg($postId),
            escapeshellarg($taxonomy),
            implode(' ', array_map('escapeshellarg', $termIds))
        ));
    }

    protected function addCategoriesToPost(int $postId, int ...$categoryIds) : string
    {
        return $this->addTermsToPost($postId, 'category', ...$categoryIds);
    }

    protected function addTagsToPost(int $postId, int ...$tagIds) : string
    {
        return $this->addTermsToPost($postId, 'post_tag', ...$tagIds);
    }

    protected function setPostTerms(int $postId, string $taxonomy, int ...$termIds) : string
    {
        return $this->cli()->wpForOutput(sprintf(
            'post term set %s %s %s --by=id',
            escapeshellarg($postId),
            escapeshellarg($taxonomy),
            implode(' ', array_map('escapeshellarg', $termIds))
        ));
    }

    protected function setPostCategories(int $postId, int ...$categoryIds) : string
    {
        return $this->setPostTerms($postId, 'category', ...$categoryIds);
    }

    protected function setPostTags(int $postId, int ...$tagIds) : string
    {
        return $this->setPostTerms($postId, 'post_tag', ...$tagIds);
    }
}
