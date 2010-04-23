<?php
    $query = $vars['query'];
    $sector = $vars['sector'];

?>
<div class='instructions'>
    <?php echo elgg_echo('search:instructions'); ?>
</div>    

<form method='GET' class='searchForm' action='org/search/'>    
    <input class='searchField' type='text' name='q' value='<?php echo escape($query); ?>'>
    <?php echo elgg_view('input/pulldown', array('internalname' => 'sector',
        'options_values' => Organization::getSectorOptions(), 
        'empty_option' => elgg_echo('sector:empty_option'),
        'value' => $vars['sector'])) 
    ?>
    <br />
    
    <?php 
        echo elgg_view('input/submit', array(
            'internalname' => 'submit',
            'value' => elgg_echo('search:submit') 
        ));
    ?>    
    
</form>

<?php   

    $latlong = null;

    if (!empty($query)) 
    {
        $geoQuery = "$query Tanzania";
        
        $latlong = elgg_geocode_location($geoQuery);
    }

    $results = '';

    if ($latlong)
    {
        $nearby = Organization::filterByArea(
            array(
                $latlong['lat'] - 1.0, 
                $latlong['long'] - 1.0, 
                $latlong['lat'] + 1.0, 
                $latlong['long'] + 1.0
            ),    
            $sector,
            $limit=1);

        if ($nearby)
        {
            $results .= "<div class='padded'>".elgg_view("org/map", array('lat' => $latlong['lat'], 'long' => $latlong['long'], 'sector' => $sector, 'nearby' => true, 'height' => 300, 'width' => 440, 'zoom' => '8'))."</div>";
        }
    }

    if (!empty($query) || $sector)
    {        
        $results .= Organization::listSearch($query, $sector, $region=null, $limit = 10);
        
        if ($results)
        {
            echo $results;
        }
        else
        {
            echo "<div class='padded'>" . elgg_echo("search:noresults") . "</div>";
        }

    }

    