<?php

    global $CONFIG;   
    
	$orgs = get_entities("group","organization", get_loggedin_user()->guid);
    
    echo elgg_view_layout('section', elgg_echo("user_orgs"),
        elgg_view('extensions/entity_list',array(
            'entities' => $orgs
        ))
    );

    echo elgg_view_layout('section', elgg_echo("actions"),
        "<a href=\"" . $CONFIG->wwwroot . "pg/org/new/" . "\">". elgg_echo('org:new') ."</a>"
    );

?>

