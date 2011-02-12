<?php

	if ($vars['count'] > $vars['threshold']) {

?>
<div class="contentWrapper"><a href="<?php echo Config::get('url'); ?>pg/search/users/?tag=<?php echo urlencode($vars['tag']); ?>"><?php 
	
		echo __("user:search:finishblurb"); 
	
	?></a></div>
<?php

	}

?>