<?php
	/**
	 * Create a submit input button	 
	 * 
	 * @uses $vars['value'] The current value, if any
	 * @uses $vars['js'] Any Javascript to enter into the input tag
	 * @uses $vars['name'] The name of the input field
	 * @uses $vars['type'] Submit or reset, defaults to submit.
	 * 
	 */

	$vars['type'] = 'submit';
    $vars['class'] = (isset($vars['class'])) ? $vars['class'] : "submit_button";
    $vars['name'] = @$vars['name'] ?: '_submit';
    
    if (!isset($vars['trackDirty']))
    {
        $vars['trackDirty'] = true;
    }
	
	echo view('input/button', $vars);
?>