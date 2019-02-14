<?php

namespace SsnTestKit;

trait ManagesTerms
{
    protected function addTermToPost(string $postId, string $taxonomy, string $termId)
    {
        return $this->cli()->wpForOutput(sprintf(
            'post term add %s %s %s --by=id',
            escapeshellarg($postId),
            escapeshellarg($taxonomy),
            escapeshellarg($termId)
        ));
    }

    protected function addCategoryToPost(string $postId, string $categoryId)
    {
        return $this->addTermToPost($postId, 'category', $categoryId);
    }

    protected function addTagToPost(string $postId, string $tagId)
    {
        return $this->addTermToPost($postId, 'post_tag', $tagId);
    }

    protected function createTerm(string $taxonomy, string $title)
    {
        return $this->cli()->wpForOutput(sprintf(
            'term create %s %s --porcelain',
            escapeshellarg($taxonomy),
            escapeshellarg($title)
        ));
    }

    protected function createCategory(string $title)
    {
        return $this->createTerm('category', $title);
    }

    protected function createTag(string $title)
    {
        return $this->createTerm('post_tag', $title);
    }
}
