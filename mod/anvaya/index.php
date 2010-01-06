
<?php    
    global $CONFIG;   

	$users = get_entities("group","organization",0,10,false);

	$area = elgg_view('extensions/entity_list',array(
		'entities' => $users
    ));
    
    $area .= "<form method='GET' action='".$CONFIG->wwwroot."pg/search/'>Tag search: <input type='text' name='tag'><input type='submit' value='search'></form>";
    
    $area .= "<form method='GET' action='".$CONFIG-->wwwroot."pg/locationSearch/'>Latitude: <input type='text' name='lat'> Longitude: <input type='text' name='long'><input type='submit' value='search'></form>";
    
    if (isloggedin())
    {
        $area .= "<p><a href='".$CONFIG->wwwroot."action/logout'>Logout</a></p>";
    }
    else
    {
        $area .= "<p><a href='".$CONFIG->wwwroot."pg/login'>Login</a></p>";
    }    
    
    $body = elgg_view_layout('one_column', $area,"");    
    
    page_draw('', $body);

?>
