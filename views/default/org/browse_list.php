<?php 

$sector = get_input('sector');     
$region = get_input('region');
$country = get_input('country');

?>
<div class='padded'>
<div class='view_toggle' style='padding-left:30px'>
    <a href='/pg/browse?list=0&sector=<?php echo escape($sector); ?>'><?php echo __('browse:map') ?></a> &middot;
    <strong><?php echo __('browse:list') ?></strong>
</div>
<?php
    echo view('org/filter_controls', array('baseurl' => '/pg/browse?list=1'));
    echo "<br />";

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