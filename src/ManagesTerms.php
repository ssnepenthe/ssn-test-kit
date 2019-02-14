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

    protected function createTerm(string $taxonomy, string $title, string $description = null)
    {
        $command = sprintf('term create %s %s', escapeshellarg($taxonomy), escapeshellarg($title));

        if ($description) {
            $command .= sprintf(' --description=%s', escapeshellarg($description));
        }

        return $this->cli()->wpForOutput($command . ' --porcelain');
    }

    protected function createCategory(string $title, string $description = null)
    {
        return $this->createTerm('category', $title, $description);
    }

    protected function createTag(string $title, string $description = null)
    {
        return $this->createTerm('post_tag', $title, $description);
    }
}
