<?php
    $query = $vars['query'];
    $sector = $vars['sector'];
    $results = $vars['results'];
    
    if (@$vars['nearby'])
    {
        $latlong = $vars['latlong'];
    
        echo "<div class='padded'>";
        
        echo "<em>".sprintf(__('search:orgs_near'), escape($query))."</em>";
        
        echo view("org/map", array(
            'lat' => $latlong['lat'], 'long' => $latlong['long'], 
            'sector' => $sector, 
            'nearby' => true, 
            'height' => 300, 'width' => 440, 
            'zoom' => '8'
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
        'options' => Organization::get_sector_options(), 
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