<div class='padded'>
<?php

$limit = 10;
$offset = (int)get_input('offset');

$sites = FeaturedSite::query()->order_by('time_created desc')->limit($limit, $offset)->filter();
$count = FeaturedSite::query()->count();

echo view('paged_list', array(
    'entities' => $sites,
    'count' => $count,
    'offset' => $offset,
    'limit' => $limit,
));        

echo "<br />";
echo "<a href='/envaya/page/featured'>".__('featured:about')."</a>";
?>
</div>