<?php

$limit = 10;
$offset = (int)get_input('offset');

$sites = FeaturedSite::all($limit, $offset);
$count = FeaturedSite::all(0,0, true);

echo elgg_view_entity_list($sites, $count, $offset, $limit, true, false, $pagination = true);