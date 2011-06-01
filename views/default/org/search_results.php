<?php
    $query = $vars['query'];
    $sector = $vars['sector'];
    $results = $vars['results'];
    
    if (@$vars['nearby'])
    {
        $latlong = $vars['latlong'];
    
        echo "<div class='padded'>";
        
        echo "<em>".sprintf(__('search:orgs_near'), escape($query))."</em>";
?>

<script type='text/javascript'>
<?php
    echo view('js/google_map');
?>  

var mapLoader = new MapLoader(function ($bounds) 
{
    var $sw = $bounds.getSouthWest();
    var $ne = $bounds.getNorthEast(); 

    return "/org/searchArea?latMin="+$sw.lat()+"&latMax="+$ne.lat()+
        "&longMin="+$sw.lng()+"&longMax="+$ne.lng()+
        "&sector=<?php echo urlencode($sector); ?>";
});

</script>
<?php        

        
        echo view("output/map", array(
            'lat' => $latlong['lat'],
            'long' => $latlong['long'], 
            'height' => 300, 
            'width' => 440, 
            'zoom' => 8,
            'onload' => 'mapLoader.setMap',
        ));
        
        echo "</div>";    
    }
    if ($results)
    {
        if (@$vars['nearby'])
        {
            echo "<div class='padded' style='padding-bottom:0px'><em>".sprintf(__('search:orgs_matching'), escape($query))."</em></div>";
        }
    
        echo $results;
    }
    else if (@!$vars['nearby'])
    {
        echo "<div class='padded'>" . __("search:noresults") . "</div>";
    }            
      
?>
<div class='padded'>
<form method='GET' class='searchForm' action='/org/search/'>    
    <?php echo view('input/text', array('name' => 'q', 'class' => 'searchField input-text', 'value' => $query)); ?>
    <?php echo view('input/pulldown', array('name' => 'sector',
        'options' => OrgSectors::get_options(), 
        'empty_option' => __('sector:empty_option'),
        'value' => $vars['sector'])) 
    ?>
    <br />
    
    <?php 
        echo view('input/submit', array(
            'value' => __('search:submit') 
        ));
    ?>    
    
</form>
</div>