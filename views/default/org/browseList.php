
<script type='text/javascript'>
function sectorChanged()
{
    setTimeout(function() {
        var sectorList = document.getElementById('sectorList');

        var val = sectorList.options[sectorList.selectedIndex].value;

        window.location.href = "org/browse?list=1&sector=" + val;
    }, 1);    
}
</script>
<?php 

$sector = get_input('sector');

echo elgg_view('input/pulldown', array(
    'internalname' => 'sector',
    'internalid' => 'sectorList',
    'options_values' => Organization::getSectorOptions(), 
    'empty_option' => elgg_echo('sector:empty_option'),
    'value' => $sector,
    'js' => "onchange='sectorChanged()' onkeypress='sectorChanged()'"        
));

echo "<br /><br />";

echo Organization::listSearch($name=null, $sector, $limit = 10);   
?>