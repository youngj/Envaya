<?php
	/**
	 * Create a reset input button
	 * @uses $vars['value'] The current value, if any
	 * @uses $vars['js'] Any Javascript to enter into the input tag
	 * @uses $vars['name'] The name of the input field
	 * @uses $vars['type'] Submit or reset, defaults to submit.
	 * 
	 */

	$vars['type'] = 'reset';
	$class = $vars['class'];
	if (!$class) $class = "submit_button";
	$vars['class'] = $class;
	
	echo view('input/button', $vars);
?>