<?php

	/**
	 * Displays a single error message
	 * @uses $vars['object'] An error message (string)
	 */
?>

	<p>
		<?php echo view('output/longtext', array('value' => $vars['object'])); ?>
	</p>