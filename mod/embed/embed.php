<?php

	// This page can only be run from within the Elgg framework
		if (!is_callable('elgg_view')) exit;
		
	// Get the name of the form field we need to inject into
		$internalname = get_input('internalname');
		
		if (!isloggedin()) exit;
		
		global $SESSION;
		
		$offset = (int) get_input('offset',0);
		$simpletype = get_input('simpletype');
		$entity_types = array('object' => array('file'));

		if (empty($simpletype)) {
			$count = get_entities('object','file',$SESSION['user']->guid,'',null,null,true);
			$entities = get_entities('object','file',$SESSION['user']->guid,'',6,$offset);
		} else {
			$count = get_entities_from_metadata('simpletype',$simpletype,'object','file',$SESSION['user']->guid,6,$offset,'',0,true);
			$entities = get_entities_from_metadata('simpletype',$simpletype,'object','file',$SESSION['user']->guid,6,$offset,'',0,false);
		}
		
		$types = get_tags(0,10,'simpletype','object','file',$SESSION['user']->guid);
		
	// Echo the embed view
		echo elgg_view('embed/media', array(
							'entities' => $entities,
							'internalname' => $internalname,
							'offset' => $offset,
							'count' => $count,
							'simpletype' => $simpletype,
							'limit' => 6,
							'simpletypes' => $types,
					   ));

?>