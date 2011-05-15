<?php 
    $baseurl = $vars['baseurl'];    
?>
<form method='GET' action='<?php echo escape($baseurl); ?>'>
<script type='text/javascript'>
function filterChanged()
{
    setTimeout(function() {
        var sectorList = $('sectorList');
        var regionList = $('regionList');
        var sector = sectorList.value;
        var region = regionList.value;
        var baseUrl = <?php echo json_encode($baseurl); ?>;
        var connector = (baseUrl.indexOf("?") == -1) ? "?" : "&";
        window.location.href = baseUrl + connector + "sector=" + sector + "&region=" + region;
    }, 1);
}
</script>
<?php

echo view('input/pulldown', array(
    'name' => 'sector',
    'id' => 'sectorList',
    'options' => OrgSectors::get_options(),
    'empty_option' => __('sector:empty_option'),
    'value' => get_input('sector'),
    'js' => "onchange='filterChanged()' onkeypress='filterChanged()' style='margin-bottom:5px'"
));

echo view('input/pulldown', array(
    'name' => 'region',
    'id' => 'regionList',
    'options' => Geography::get_region_options('tz'),
    'empty_option' => __('region:empty_option'),
    'value' => get_input('region'),
    'js' => "onchange='filterChanged()' onkeypress='filterChanged()'  style='margin-bottom:5px'"
));
    
?>
<noscript>
<?php echo view('input/submit', array('value' => __('go'))); ?>
</noscript>
</form>
