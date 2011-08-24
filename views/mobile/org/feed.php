<?php

$items = array_slice($vars['items'], 0, 10);

?>
<?php

echo view('org/current_filter', array(
    'filters' => $vars['filters'], 
    'changeurl' => '/pg/change_feed_view'
));
echo view('feed/list', array('items' => $items));
