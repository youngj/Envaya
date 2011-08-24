<?php
    $query = $vars['query'];
    $filters = $vars['filters'];
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

function initMap(map)
{
<?php
    $viewport = $latlong['viewport'];
    if ($viewport)
    {
?>
    map.fitBounds(new google.maps.LatLngBounds(
        new google.maps.LatLng(<?php echo $viewport['southwest']['lat'] ?>,<?php echo $viewport['southwest']['lng'] ?>),
        new google.maps.LatLng(<?php echo $viewport['northeast']['lat'] ?>,<?php echo $viewport['northeast']['lng'] ?>)
    ));
<?php
    }
?>
    var orgLoader = new OrgMapLoader();

    orgLoader.getURLParams = function() {
        return {sector: <?php echo json_encode($filters[0]->value); ?>};
    };
    orgLoader.setMap(map);
}

</script>
<?php        

        echo view("output/map", array(
            'lat' => $latlong['lat'],
            'long' => $latlong['long'], 
            'height' => 300, 
            'width' => 540, 
            'zoom' => 9,
            'onload' => 'initMap',
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
<?php
    echo view('org/search_form', array('query' => $query, 'filters' => $filters));
?>
</div>