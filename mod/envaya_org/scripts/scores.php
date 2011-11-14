<?php

/*
 * Command-line script that prints out website scores to help figure out 
 * which ones to consider for a Featured Organization award
 */

$root = dirname(dirname(dirname(__DIR__)));
 
require_once("$root/scripts/cmdline.php");
require_once("$root/start.php");

$orgs = Organization::query()->where('approval > 0')->filter();

$recent = timestamp() - 86400 * 31;

$scores = array();
foreach ($orgs as $org)
{
    $score = TodoItem::get_total_score($org);
    
    if ($org->query_feed_items()->where('time_posted > ?', $recent)->exists())
    {
        $score += 1;
    }

    $scores[$score][] = $org;
}

$featured_users = array();
foreach (FeaturedSite::query()->filter() as $site)
{
    $featured_users[$site->container_guid] = true;
}

ksort($scores, SORT_NUMERIC);

$rscores = array_reverse($scores, true);

$num_printed = 0;

foreach ($rscores as $score => $orgs)
{
    $str_score = sprintf("%2d", $score);
    $num_orgs = sizeof($orgs);

    if ($num_printed < 40)
    {
        foreach ($orgs as $org)
        {
			$url = abs_url($org->get_url());
		
            echo $str_score.(@$featured_users[$org->guid] ? "*" : " ")." $url {$org->name}\n";
        }
    }
    else
    {
        echo "$str_score  ($num_orgs organizations)\n";
    }
    
    $num_printed += $num_orgs;
}
