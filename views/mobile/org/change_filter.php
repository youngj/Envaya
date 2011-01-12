<?php

$sector = $vars['sector'];
$region = $vars['region'];
$baseurl = $vars['baseurl'];
 
?>

<form method='GET' action='<?php echo $baseurl; ?>'>
<div>
<?php

echo view('input/pulldown', array(
    'internalname' => 'sector',
    'options' => Organization::get_sector_options(),
    'empty_option' => __('sector:empty_option'),
    'value' => $sector
));

?>
</div>
<div>
<?php

echo view('input/pulldown', array(
    'internalname' => 'region',
    'options' => regions_in_country('tz'),
    'empty_option' => __('region:empty_option'),
    'value' => $region,
));
    
?>
</div>
<div>
<?php
    
echo view('input/submit', array('internalname' => 'submit', 'value' => __('go'))); 
?>
</div>
</form>
