<?php

	/**
	 * Elgg exception
	 * Displays a single exception
	 * 
	 * @package Elgg
	 * @subpackage Core

	 * @author Curverider Ltd

	 * @link http://elgg.org/
	 * 
	 * @uses $vars['object'] An exception
	 */

	global $CONFIG;
	 
	$class = get_class($vars['object']);
	$message = elgg_view('output/longtext', array('value' => $vars['object']->getMessage()));
	
	$body = <<< END
		<span title="$class">
			<b>$message</b>
		</span>
END;

	if ($CONFIG->debug)
	{
		$details = elgg_view('output/longtext', array('value' => print_r($vars['object'], true)));
		$body .= <<< END
		<hr />
		<p class="messages-exception-detail">
			$details
		</p>
END;
	}
	
	$title = $class;
	
	echo elgg_view_layout("one_column_padded", elgg_view_title($title), $body);
?>