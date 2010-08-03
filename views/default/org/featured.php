<?php

$limit = 10;
$offset = (int)get_input('offset');

$sites = FeaturedSite::query()->limit($limit, $offset)->filter();
$count = FeaturedSite::query()->count();

echo view_entity_list($sites, $count, $offset, $limit, true);