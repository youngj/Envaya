<?php

/*
 * Command-line script that prints out website scores to help figure out 
 * which ones to consider for a Featured Organization award
 */

require_once("scripts/cmdline.php");
require_once("engine/start.php");


$scores = array();
foreach (Organization::query()->filter() as $org)
{
    $scores[$org->get_website_score()][] = $org;
}

$featured_users = array();
foreach (FeaturedSite::query()->filter() as $site)
{
    $featured_users[$site->container_guid] = true;
}

for ($i = 0; $i < 100; $i++)
{
    $orgs = @$scores[$i];
    if ($orgs)
    {
        foreach ($orgs as $org)
        {
            echo sprintf("%2d", $i).(@$featured_users[$org->guid] ? "+" : " ")." {$org->get_url()} {$org->name}\n";
        }
    }
}
