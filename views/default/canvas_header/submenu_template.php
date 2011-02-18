<?php

	if (@$vars['selected']) {
		$selected = "class=\"selected\"";
	} else {
		$selected = "";
	}
	
?>
<li <?php echo $selected; ?>><a href="<?php echo escape($vars['href']); ?>"><?php echo escape($vars['label']); ?></a></li>