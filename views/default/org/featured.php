<?php

$limit = 10;
$offset = (int)get_input('offset');

$sites = FeaturedSite::query()->order_by('time_created desc')->limit($limit, $offset)->filter();
$count = FeaturedSite::query()->count();

echo view_entity_list($sites, $count, $offset, $limit, true);

echo "<br />";
echo "<a href='/envaya/featured'>".__('featured:about')."</a>";
