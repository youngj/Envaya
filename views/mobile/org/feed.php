<?php

$sector = $vars['sector'];
$region = $vars['region'];
$items = array_slice($vars['items'], 0, 10);

?>
<?php

echo view('org/current_filter', array('sector' => $sector, 'region' => $region, 'changeurl' => '/org/change_feed_view'));
echo view('feed/list', array('items' => $items));
