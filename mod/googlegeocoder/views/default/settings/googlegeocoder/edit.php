<p>
	<h3><?php echo elgg_echo('googlegeocoder:googleapi'); ?>: </h3>
	<?php
		echo elgg_view('input/text', array('internalname' => 'params[google_api]', 'value' => $vars['entity']->google_api));
	?>
</p>