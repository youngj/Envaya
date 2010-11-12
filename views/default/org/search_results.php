<?php
    $query = $vars['query'];
    $sector = $vars['sector'];
    $results = $vars['results'];
    
    if (@$vars['nearby'])
    {
        $latlong = $vars['latlong'];
    
        echo "<div class='padded'>".view("org/map", array('lat' => $latlong['lat'], 'long' => $latlong['long'], 'sector' => $sector, 'nearby' => true, 'height' => 300, 'width' => 440, 'zoom' => '8'))."</div>";    
    }
    if ($results)
    {
        echo $results;
    }
    else
    {
        echo "<div class='padded'>" . __("search:noresults") . "</div>";
    }            
      
?>
<div class='padded'>
<form method='GET' class='searchForm' action='/org/search/'>    
    <?php echo view('input/text', array('internalname' => 'q', 'class' => 'searchField input-text', 'value' => $query)); ?>
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
</div>