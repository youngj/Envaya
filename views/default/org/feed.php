<div class='padded'>
<?php

$sector = get_input('sector');
$region = get_input('region');

$orgs = Organization::search('', $sector, $region, $limit = 100);

$updates = NewsUpdate::filterByOrganizations($orgs, $limit = 20);


?>

<script type='text/javascript'>
function sectorChanged()
{
    var sectorList = document.getElementById('sectorList');
    var regionList = document.getElementById('regionList');
    var sector = sectorList.options[sectorList.selectedIndex].value;
    var region = regionList.options[regionList.selectedIndex].value;
    window.location.href = "/org/feed?sector=" + sector + "&region=" + region;
}
</script>

<?php 

echo elgg_view('input/pulldown', array(
    'internalname' => 'sector',
    'internalid' => 'sectorList',
    'options_values' => Organization::getSectorOptions(), 
    'empty_option' => elgg_echo('sector:empty_option'),
    'value' => $sector,
    'js' => "onchange='sectorChanged()' onkeypress='sectorChanged()'"        
));

echo elgg_view('input/pulldown', array(
    'internalname' => 'region',
    'internalid' => 'regionList',    
    'options_values' => regions_in_country('tz'),
    'empty_option' => elgg_echo('region:empty_option'),
    'value' => $region,
    'js' => "onchange='sectorChanged()' onkeypress='sectorChanged()'"        
));

?>

</div>

<?php


if (empty($updates))
{
    echo "<div class='padded'>".elgg_echo("search:noresults")."</div>";
}


foreach ($updates as $update)
{
    $org = $update->getContainerEntity();
?>

<div class='blog_post_wrapper'>
<div class="feed_post">    
    <?php 
        $orgIcon = $org->getIcon('small');
        $orgUrl = $org->getURL();
        $url = $update->getURL();
        
        echo "<a class='feed_org_icon' href='$orgUrl'><img src='{$orgIcon}'/></a>";
                
        echo "<div class='feed_content'>";
        
        if ($update->hasImage())
        {
            echo "<a class='smallBlogImageLink' style='float:right' href='$url'><img src='{$update->getImageURL('small')}' /></a>";            
        }               
        
        echo "<a class='feed_org_name' href='$orgUrl'>".escape($org->name)."</a>: ";
       
        $maxLength = 500; 
       
        echo elgg_view('output/longtext', array('value' => $update->getSnippetHTML($maxLength))); 
        
        if (strlen($update->content) > $maxLength)
        {                  
            echo " <a class='feed_more' href='$url'>".elgg_echo('blog:more')."</a>";
        }    
        
        echo "<div class='blog_date'>{$update->getDateText()}</div>";
        echo "</div>";
    ?>              
    <div style='clear:both'></div>
</div>
</div>

<?php

}

?>