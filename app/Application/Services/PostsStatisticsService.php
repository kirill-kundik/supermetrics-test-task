<?php


class PostsStatisticsService
{
    private ?array $posts;
    private array $stats;

    public function __construct(?array $posts = null)
    {
        $this->posts = $posts;
        $this->stats = [];

        if (!is_null($this->posts))
            $this->countStats();
    }

    public function getStats()
    {
        return $this->stats;
    }

    public function updateStats($post)
    {
        $postDate = PostsStatisticsService::parsePostDate($post);

        $yearsDict = $this->stats["years"] ?? [];
        $usersDict = $this->stats["users"] ?? [];

        $usersDict = PostsStatisticsService::updateUserStats($postDate, $post, $usersDict);
        $yearsDict = PostsStatisticsService::updateYearsStats($postDate, $post, $yearsDict);

        $this->stats["years"] = $yearsDict;
        $this->stats["users"] = $usersDict;
    }

    private function countStats()
    {
        foreach ($this->posts as $post) {
            $this->updateStats($post);
        }
    }

    private static function parsePostDate($post)
    {
        $postDate = DateTime::createFromFormat(
            DateTime::ISO8601, $post["created_time"]
        );
        $postYear = (int)$postDate->format("Y");
        $postMonth = (int)$postDate->format("m");
        $postWeek = (int)$postDate->format("W");

        return ["year" => $postYear, "month" => $postMonth, "week" => $postWeek];
    }

    private static function updateYearsStats($postDate, $post, $yearsDict)
    {
        $yearDict = $yearsDict[$postDate["year"]] ?? [];
        $monthsDict = $yearDict["months"] ?? [];
        $weeksDict = $yearDict["weeks"] ?? [];

        $monthsDict = PostsStatisticsService::updateMonthsStats($postDate, $post, $monthsDict);
        $weeksDict = PostsStatisticsService::updateWeeksStats($postDate, $post, $weeksDict);

        $yearDict["months"] = $monthsDict;
        $yearDict["weeks"] = $weeksDict;

        $yearsDict[$postDate["year"]] = $yearDict;

        return $yearsDict;
    }

    private static function updateMonthsStats($postDate, $post, $monthsDict)
    {
        $month = $postDate["month"];

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
        return $monthsDict;
    }

    private static function updateWeeksStats($postDate, $post, $weeksDict)
    {
        $week = $postDate["week"];

        $weekDict = $weeksDict[$week] ?? [];
        $weekCount = $weekDict["total_posts"] ?? 0;
        $weekDict["total_posts"] = $weekCount + 1;
        $weeksDict[$week] = $weekDict;

        return $weeksDict;
    }

    private static function updateUserStats($postDate, $post, $usersDict)
    {
        $userDict = $usersDict[$post["from_id"]] ?? [];
        if (empty($userDict)) {
            $userDict["name"] = $post["from_name"];
            $userDict["id"] = $post["from_id"];
        }

        $postYear = $postDate["year"];
        $postMonth = $postDate["month"];
        $statDate = $postYear . "-" . $postMonth;

        $userPostsStatsDates = $userDict["stats_dates"] ?? [];

        if (!in_array($statDate, $userPostsStatsDates)) {
            $userPostsStatsDates[] = $statDate;
            $userDict["stats_dates"] = $userPostsStatsDates;
        }

        $userTotalPostsCount = $userDict["total_posts"] ?? 0;
        $userDict["total_posts"] = $userTotalPostsCount + 1;
        $userDict["average_per_month"] = $userTotalPostsCount / count($userDict["stats_dates"]);

        $usersDict[$post["from_id"]] = $userDict;

        return $usersDict;
    }
}