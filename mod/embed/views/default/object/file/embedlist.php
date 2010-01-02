<?php

	$file = $vars['entity'];
	$friendlytime = friendly_time($vars['entity']->time_created);
	
?>
	<div id="embedFile<?php echo $file->guid; ?>">
<?php
	
	$info = "<p> <a href=\"{$file->getURL()}\">{$file->title}</a></p>";
	$info .= "<p class=\"owner_timestamp\">{$friendlytime}";	
	$icon = "<a href=\"{$file->getURL()}\">" . elgg_view("file/icon", array("mimetype" => $file->mimetype, 'thumbnail' => $file->thumbnail, 'file_guid' => $file->guid, 'size' => 'small')) . "</a>";
	
	echo elgg_view('search/listing',array('icon' => $icon, 'info' => $info));	

?>
	</div>