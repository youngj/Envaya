<?php

$sector = $vars['sector'];
$region = $vars['region'];
$country = $vars['country'];
$baseurl = $vars['baseurl'];
 
?>

<form method='GET' action='<?php echo $baseurl; ?>'>
<div>
<?php

echo view('input/pulldown', array(
    'name' => 'sector',
    'options' => OrgSectors::get_options(),
    'empty_option' => __('sector:empty_option'),
    'value' => $sector
));

?>
</div>
<div>
<?php
echo view('input/pulldown', array(
    'name' => 'country',
    'options' => Geography::get_country_options(),
    'empty_option' => __('country:empty_option'),
    'value' => $country,
));
?>
</div>
<div>
<?php
if ($country)
{
    echo view('input/pulldown', array(
        'name' => 'region',
        'options' => Geography::get_region_options($country),
        'empty_option' => __('region:empty_option'),
        'value' => $region,
    ));
}
?>
</div>
<div>
<?php
    
echo view('input/submit', array('value' => __('go'))); 
?>
</div>
</form>
