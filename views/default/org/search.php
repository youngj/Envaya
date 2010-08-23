<?php
    $query = $vars['query'];
    $sector = $vars['sector'];

?>
<div class='instructions'>
    <?php echo __('search:instructions'); ?>
</div>    

<form method='GET' class='searchForm' action='org/search/'>    
    <input class='searchField' type='text' name='q' value='<?php echo escape($query); ?>'>
    <?php echo view('input/pulldown', array('internalname' => 'sector',
        'options_values' => Organization::get_sector_options(), 
        'empty_option' => __('sector:empty_option'),
        'value' => $vars['sector'])) 
    ?>
    <br />
    
    <?php 
        echo view('input/submit', array(
            'internalname' => 'submit',
            'value' => __('search:submit') 
        ));
    ?>    
    
</form>

<?php   

    $latlong = null;

    if (!empty($query)) 
    {
        $geoQuery = "$query Tanzania";
        
        $latlong = Geocoder::geocode($geoQuery);
    }

    $results = '';

    if ($latlong)
    {
        $nearby = Organization::query_by_area(
            array(
                $latlong['lat'] - 1.0, 
                $latlong['long'] - 1.0, 
                $latlong['lat'] + 1.0, 
                $latlong['long'] + 1.0
            ),    
            $sector)->limit(1)->get();

        if ($nearby)
        {
            $results .= "<div class='padded'>".view("org/map", array('lat' => $latlong['lat'], 'long' => $latlong['long'], 'sector' => $sector, 'nearby' => true, 'height' => 300, 'width' => 440, 'zoom' => '8'))."</div>";
        }
    }

    if (!empty($query) || $sector)
    {        
        $results .= Organization::list_search($query, $sector, $region=null, $limit = 10);
        
        if ($results)
        {
            echo $results;
        }
        else
        {
            echo "<div class='padded'>" . __("search:noresults") . "</div>";
        }

    }

    