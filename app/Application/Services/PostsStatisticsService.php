<?php


class PostsStatisticsService
{
    private array $posts;
    private array $stats;

    public function __construct(array $posts)
    {
        $this->posts = $posts;
        $this->stats = [];
        $this->countStats();
    }

    public function getStats()
    {
        return $this->stats;
    }

    private function countStats()
    {
        foreach ($this->posts as $post) {
            $postDate = $this->parsePostDate($post);

            $this->updateStats($postDate, $post);
        }
        $this->stats = ["years" => $this->stats];
    }

    private function parsePostDate($post)
    {
        $postDate = DateTime::createFromFormat(
            DateTime::ISO8601, $post["created_time"]
        );
        $postYear = (int)$postDate->format("Y");
        $postMonth = (int)$postDate->format("m");
        $postWeek = (int)$postDate->format("W");

        return ["year" => $postYear, "month" => $postMonth, "week" => $postWeek];
    }

    private function updateStats($postDate, $post)
    {
        $yearDict = $this->stats[$postDate["year"]] ?? [];
        $usersDict = $this->stats["users"] ?? [];
        $monthsDict = $yearDict["months"] ?? [];
        $weeksDict = $yearDict["weeks"] ?? [];

        PostsStatisticsService::updateMonthsStats($postDate["month"], $post, $monthsDict);
        PostsStatisticsService::updateWeeksStats($postDate["week"], $post, $weeksDict);
        PostsStatisticsService::updateUserStats($postDate, $post, $usersDict);

        $yearDict["months"] = $monthsDict;
        $yearDict["weeks"] = $weeksDict;
        $yearDict["users"] = $usersDict;
        $this->stats[$postDate["year"]] = $yearDict;
    }

    private static function updateMonthsStats($month, $post, &$monthsDict)
    {
        $monthDict = $monthsDict[$month] ?? [];
        $monthTotalPosts = $monthDict["total_posts"] ?? 0;
        $monthMaxPostLength = $monthDict["max_post_length"] ?? -1;
        $monthTotalPostsLength = $monthDict["total_posts_length"] ?? 0;

        $postLength = strlen($post["message"]);
        $monthTotalPostsLength += $postLength;
        $monthTotalPosts += 1;
        if ($postLength > $monthMaxPostLength) {
            $monthDict["max_post_length"] = $postLength;
            $monthDict["longest_post"] = $post;
        }
        $monthDict["total_posts"] = $monthTotalPosts;
        $monthDict["total_posts_length"] = $monthTotalPostsLength;
        $monthDict["average_posts_length"] = $monthTotalPostsLength / $monthTotalPosts;

        $monthsDict[$month] = $monthDict;
    }

    private static function updateWeeksStats($week, $post, &$weeksDict)
    {
        $weekDict = $weeksDict[$week] ?? [];
        $weekCount = $weekDict["total_posts"] ?? 0;
        $weekDict["total_posts"] = $weekCount + 1;
        $weeksDict[$week] = $weekDict;
    }

    private static function updateUserStats($postDate, $post, &$usersDict)
    {
        $userDict = $usersDict[$post["from_id"]] ?? [];
        if (count($userDict) == 0) {
            $userDict["name"] = $post["from_name"];
            $userDict["id"] = $post["from_id"];
        }

        $userMonthsDict = $userDict["months"] ?? [];
        $userMonthCount = $userMonthsDict[$postDate["month"]] ?? 0;
        $userMonthsDict[$postDate["month"]] = $userMonthCount + 1;

        $userDict["months"] = $userMonthsDict;
        $usersDict[$post["from_id"]] = $userDict;
    }
}