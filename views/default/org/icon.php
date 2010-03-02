<?php

	$group = $vars['entity'];

	if ($group instanceof Organization) {

	// Get size
	if (!in_array($vars['size'],array('small','medium','large','tiny','master','topbar')))
		$vars['size'] = "medium";

	// Get any align and js
	if (!empty($vars['align'])) {
		$align = " align=\"{$vars['align']}\" ";
	} else {
		$align = "";
	}

	if ($icontime = $vars['entity']->icontime) {
		$icontime = "{$icontime}";
	} else {
		$icontime = "default";
	}


?>

<div class="groupicon">
<a href="<?php echo $vars['entity']->getURL(); ?>" class="icon" ><img src="<?php echo $vars['entity']->getIcon($vars['size']); ?>" border="0" <?php echo $align; ?> title="<?php echo $name; ?>" <?php echo $vars['js']; ?> /></a>
</div>

<?php

	}

?>