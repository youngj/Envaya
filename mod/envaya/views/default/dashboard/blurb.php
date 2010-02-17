<?php

    global $CONFIG;   
    
    
	$users = get_entities("group","organization", get_loggedin_user()->guid);

	$area = elgg_view('extensions/entity_list',array(
		'entities' => $users
    ));
    
    echo $area;
    
    echo "</p><br><br><p><a href=\"" . $CONFIG->wwwroot . "pg/org/new/" . "\">". elgg_echo('org:new') ."</a></p>";

?>

