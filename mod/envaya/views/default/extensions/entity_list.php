<?php

	$entities = $vars['entities'];

	foreach($entities as $entity) {
		echo  "<p><a href='".$entity->getUrl()."'>".$entity->name."</a></p>";
	}


?>
