
<?php    
    global $CONFIG;   

	$orgs = get_entities("group","organization",0,10,false);

    $area = "<div><a href='{$CONFIG->wwwroot}pg/org/new'>".elgg_echo("register_org")."</a></div>";
    
    $area .= "<form method='GET' action='".$CONFIG->wwwroot."pg/org/search/'><input type='text' name='q'><input type='submit' value='".elgg_echo('search')."'></form>";
    
    $body = elgg_view_layout('one_column_padded', elgg_view_title(elgg_echo("welcome")), $area);    
    
    page_draw('', $body);

?>
