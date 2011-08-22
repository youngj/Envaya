<?php 

$sector = get_input('sector');
$region = get_input('region');
$country = get_input('country');

echo view('org/current_filter', array(
    'sector' => $sector, 
    'country' => $country,
    'region' => $region, 
    'changeurl' => '/pg/change_browse_view'));

?>

<div class='padded'>
<?php
$res = view('org/search_list', array(    
    'sector' => $sector,
    'region' => $region,
    'country' => $country,
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