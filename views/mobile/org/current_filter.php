<?php
    $sector = $vars['sector'];
    $region = $vars['region'];
    $changeFilterUrl = $vars['changeurl'];
?>

<div class='padded' style='border-bottom:1px solid #ccc;'>
<?php
$change_view_link_start = "<a href='$changeFilterUrl?sector=".escape($sector)."&region=".escape($region)."'>";
$change_view_link_end = "</a>";

echo __('org:sector') . ": ".$change_view_link_start.
view('output/pulldown', array(
    'options' => OrgSectors::get_options(),
    'value' => $sector,
    'empty_option' => __('sector:all')
)).$change_view_link_end;   
?>
<br />
<?php
echo __('org:region') . ": ". $change_view_link_start. view('output/pulldown', array(
    'options' => Geography::get_region_options('tz'),
    'value' => $region,
    'empty_option' => __('region:all')
)). $change_view_link_end;
?>

</div>
