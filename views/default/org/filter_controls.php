<?php 
    $baseurl = $vars['baseurl'];   

    $country_code = get_input('country');
    if ($country_code && !Geography::is_available_country($country_code))
    {
        $country_code = null;
    }
?>
<form method='GET' action='<?php echo escape($baseurl); ?>'>
<script type='text/javascript'>
function filterChanged()
{
    setTimeout(function() {
        var sectorList = $('sectorList');
        var countryList = $('countryList');
        var regionList = $('regionList');
        var sector = sectorList.value;
        var country = countryList.value;
        
        var baseUrl = <?php echo json_encode($baseurl); ?>;
        var connector = (baseUrl.indexOf("?") == -1) ? "?" : "&";

        var url = baseUrl + connector + "sector=" + sector + "&country=" + country;
        
        if (regionList && country)
        {
            var region = regionList.value;
            url += "&region=" + region;
        }
        
        window.location.href = url;
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
    'style' => 'margin-bottom:5px',
    'attrs' => array(
        'onchange' => 'filterChanged()', 
        'onkeypress' => 'filterChanged()'
    )
));
echo " ";
echo view('input/pulldown', array(
    'name' => 'country',
    'id' => 'countryList',
    'options' => Geography::get_country_options(),
    'empty_option' => __('country:empty_option'),
    'value' => $country_code,
    'style' => 'margin-bottom:5px',
    'attrs' => array(
        'onchange' => 'filterChanged()', 
        'onkeypress' => 'filterChanged()'
    )
));
echo " ";
if ($country_code)
{
    echo view('input/pulldown', array(
        'name' => 'region',
        'id' => 'regionList',
        'options' => Geography::get_region_options($country_code),
        'empty_option' => __('region:empty_option'),
        'value' => get_input('region'),
        'style' => 'margin-bottom:5px',
        'attrs' => array(
            'onchange' => 'filterChanged()', 
            'onkeypress' => 'filterChanged()'
        )
    ));
}
    
?>
<noscript>
<?php echo view('input/submit', array('value' => __('go'))); ?>
</noscript>
</form>
