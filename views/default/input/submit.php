<?php
	/**
	 * Create a submit input button
	 */
    
    if (!isset($vars['name']))
    {
        $vars['name'] = '_submit';
    }
    
    if (!isset($vars['track_dirty']))
    {
        $vars['track_dirty'] = true;
    }
	
	echo view('input/button', $vars);
    