
<div class='instructions'>
    <?php echo elgg_echo('search:instructions'); ?>
</div>    

<form method='GET' action='<?php echo $CONFIG->wwwroot ?>pg/org/search/'>
    <input type='text' name='q' value='<?php echo escape($query); ?>'>
    <input type='submit' value='<?php echo elgg_echo('search:submit') ?>'>
</form>

<?php
    $query = $vars['query'];

    $latlong = null;

    if (!empty($query)) 
    {
        $latlong = elgg_geocode_location($query);
    }

    $results = '';

    if ($latlong)
    {
        $radius = 2.0;

        $nearby = Organization::getUsersInArea(
            array(
                $latlong['lat'] - 1.0, 
                $latlong['long'] - 1.0, 
                $latlong['lat'] + 1.0, 
                $latlong['long'] + 1.0
            ),    
            $limit=1);

        if ($nearby)
        {
            $results .= "<div class='padded'>".elgg_view("org/map", array('lat' => $latlong['lat'], 'long' => $latlong['long'], 'pin' => 'true', 'nearby' => true, 'height' => 300, 'width' => 440, 'zoom' => '8'))."</div>";
        }
    }

    if (!empty($query))
    {
        $results .= Organization::listUserSearch(elgg_strtolower($query), $limit = 10);
        
        if ($results)
        {
            echo $results;
        }
        else
        {
            echo "<div class='padded'>" . elgg_echo("org:searchnoresults") . "</div>";
        }

    }

    