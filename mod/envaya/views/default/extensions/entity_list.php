<?php

	$entities = $vars['entities'];

	foreach($entities as $entity) {
	    
	    if($entity->userCanSee())
	    {
		    echo  "<p><a href='".$entity->getUrl()."'>".escape($entity->name)."</a>" . (!$entity->isApproved() ? (" (" . elgg_echo('org:shortnotapproved') .") ") : "") . "</p>";
	    }
	}


?>
