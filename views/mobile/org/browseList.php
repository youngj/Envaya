<?php 

$sector = @$vars['sector'];
$region = @$vars['region'];

echo view('org/current_filter', array('sector' => $sector, 'region' => $region, 'changeurl' => '/org/change_browse_view'));

?>

<div class='padded'>
<?php
$res = view('org/search_list', array(    
    'sector' => $sector,
    'region' => $region
));

if ($res)
{
    echo $res;
}
else
{
    echo __("search:noresults");
}

?>
</div>