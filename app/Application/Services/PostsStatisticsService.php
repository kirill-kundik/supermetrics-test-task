<?php


class PostsStatisticsService
{
    private array $posts;
    private array $stats;

    public function __construct(array $posts)
    {
        $this->posts = $posts;

        $this->countStats();
    }

    public function getStats()
    {
        return $this->stats;
    }

    private function countStats() {

    }
}