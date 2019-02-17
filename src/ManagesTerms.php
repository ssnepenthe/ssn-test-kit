<?php

namespace SsnTestKit;

trait ManagesTerms
{
    protected function addTermsToPost(int $postId, string $taxonomy, int ...$termIds)
    {
        return $this->cli()->wpForOutput(sprintf(
            'post term add %s %s %s --by=id',
            escapeshellarg($postId),
            escapeshellarg($taxonomy),
            implode(' ', array_map('escapeshellarg', $termIds))
        ));
    }

    protected function addCategoriesToPost(int $postId, int ...$categoryIds)
    {
        return $this->addTermsToPost($postId, 'category', ...$categoryIds);
    }

    protected function addTagsToPost(int $postId, int ...$tagIds)
    {
        return $this->addTermsToPost($postId, 'post_tag', ...$tagIds);
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

    // @todo Any reason to accept format? Probably not...
    protected function generateTerms(string $taxonomy, int $count = 1, int $maxDepth = 1)
    {
        // @todo Consider accepting args array for misc options?
        $termIds = $this->cli()->wpForOutput(sprintf(
            'term generate %s --count=%s --max_depth=%s --format=ids',
            escapeshellarg($taxonomy),
            escapeshellarg($count),
            escapeshellarg($maxDepth)
        ));

        return array_map('intval', explode(' ', $termIds));
    }

    protected function generateCategories(int $count = 1, int $maxDepth = 1)
    {
        return $this->generateTerms('category', $count, $maxDepth);
    }

    protected function generateTags(int $count = 1, int $maxDepth = 1)
    {
        return $this->generateTerms('post_tag', $count, $maxDepth);
    }

    protected function setPostTerms(int $postId, string $taxonomy, int ...$termIds)
    {
        return $this->cli()->wpForOutput(sprintf(
            'post term set %s %s %s --by=id',
            escapeshellarg($postId),
            escapeshellarg($taxonomy),
            implode(' ', array_map('escapeshellarg', $termIds))
        ));
    }

    protected function setPostCategories(int $postId, int ...$categoryIds)
    {
        return $this->setPostTerms($postId, 'category', ...$categoryIds);
    }

    protected function setPostTags(int $postId, int ...$tagIds)
    {
        return $this->setPostTerms($postId, 'post_tag', ...$tagIds);
    }
}
