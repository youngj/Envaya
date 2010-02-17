
<?php    
    global $CONFIG;   

	$orgs = get_entities("group","organization",0,10,false);

	$area = elgg_view('extensions/entity_list',array(
		'entities' => $orgs
    ));
    
    $area .= "<form method='GET' action='".$CONFIG->wwwroot."pg/org/search/'><input type='text' name='q'><input type='submit' value='".elgg_echo('search')."'></form>";
    
    //$area .= "<form method='GET' action='".$CONFIG->wwwroot."pg/locationSearch/'>Latitude: <input type='text' name='lat'> Longitude: <input type='text' name='long'><input type='submit' value='search'></form>";   
    
    $body = elgg_view_layout('one_column_padded', elgg_view_title(elgg_echo("welcome")), $area);    
    
    page_draw('', $body);

?>
