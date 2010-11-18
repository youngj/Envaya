<?php

	$entities = $vars['entities'];
	if (is_array($entities) && sizeof($entities) > 0) {
		foreach($entities as $entity)
			echo view_entity($entity);
	}

?>