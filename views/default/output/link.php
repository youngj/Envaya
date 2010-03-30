<?php

	$link = $vars['href'];
	
	if ($vars['is_action'])
	{
		$ts = time();
		$token = generate_action_token($ts);
    	
    	$sep = "?";
		if (strpos($link, '?')>0) $sep = "&";
		$link = "$link{$sep}__elgg_token=$token&__elgg_ts=$ts";
	}
	
	if ($vars['class']) {
		$class = 'class="' . $vars['class'] . '"';
	} else {
		$class = '';
	}
?>
<a href="<?php echo $link; ?>" <?php echo $class; ?>><?php echo escape($vars['text']); ?></a>