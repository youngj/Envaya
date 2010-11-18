<?php

	/**
	 * Displays a single system message
	 * @uses $vars['object'] A system message (string)
	 */
?>

	<p>
		<?php echo view('output/longtext', array('value' => $vars['object'])); ?>
	</p>