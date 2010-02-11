<div id="dashboard_info">
<p>
<?php

    global $CONFIG;   
    
    echo "<b>Welcome, " . get_loggedin_user()->name . "!</b><br>";
    

	$users = get_entities("group","organization",0,10,false);

	$area = elgg_view('extensions/entity_list',array(
		'entities' => $users
    ));
    
    echo $area;
    
    echo "</p><br><br><p><a href=\"" . $CONFIG->wwwroot . "pg/org/new/" . "\">". elgg_echo('org:new') ."</a></p>";

?>

</div>