<?php

namespace SsnTestKit;

trait ManagesTerms
{
    protected function createTerm(string $taxonomy, string $title, string $description = null) : string
    {
        $command = sprintf('term create %s %s', escapeshellarg($taxonomy), escapeshellarg($title));

        if ($description) {
            $command .= sprintf(' --description=%s', escapeshellarg($description));
        }

        return $this->cli()->wpForOutput($command . ' --porcelain');
    }

    protected function createCategory(string $title, string $description = null) : string
    {
        return $this->createTerm('category', $title, $description);
    }

    protected function createTag(string $title, string $description = null) : string
    {
        return $this->createTerm('post_tag', $title, $description);
    }

    // @todo Any reason to accept format? Probably not...
    protected function generateTerms(string $taxonomy, int $count = 1, int $maxDepth = 1) : string
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

    protected function generateCategories(int $count = 1, int $maxDepth = 1) : string
    {
        return $this->generateTerms('category', $count, $maxDepth);
    }

    protected function generateTags(int $count = 1, int $maxDepth = 1) : string
    {
        return $this->generateTerms('post_tag', $count, $maxDepth);
    }
}
