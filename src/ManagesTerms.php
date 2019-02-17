<?php

namespace SsnTestKit;

trait ManagesTerms
{
    protected function addTermToPost(int $postId, string $taxonomy, int $termId)
    {
        return $this->cli()->wpForOutput(sprintf(
            'post term add %s %s %s --by=id',
            escapeshellarg($postId),
            escapeshellarg($taxonomy),
            escapeshellarg($termId)
        ));
    }

    protected function addCategoryToPost(int $postId, int $categoryId)
    {
        return $this->addTermToPost($postId, 'category', $categoryId);
    }

    protected function addTagToPost(int $postId, int $tagId)
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

        if (1 === $count) {
            return (int) $termIds;
        }

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
}
