<?php

	/**
	 * Simple cache viewer
	 * Bypasses the engine to view simple cached CSS views.
	 * 
	 * @package Elgg
	 * @subpackage Core
	 * @author Curverider Ltd
	 * @link http://elgg.org/
	 */

    require_once(dirname(dirname(__FILE__)). '/engine/settings.php');
		
    global $CONFIG, $viewinput, $override;
    if (!isset($override)) $override = false;
		
    $contents = '';
    if (!isset($viewinput)) $viewinput = $_GET;

    $view = $viewinput['view'];
    $viewtype = $viewinput['viewtype'];
    if (empty($viewtype)) $viewtype = 'default';        
        
    $simplecache_enabled = $CONFIG->simplecache_enabled;
    $dataroot = $CONFIG->dataroot;
                
    if ($simplecache_enabled || $override) 
    {
        $filename = $dataroot . 'views_simplecache/' . md5($viewtype . $view);
        if (file_exists($filename)) {
            $contents = file_get_contents($filename);
            header("Content-Length: " . strlen($contents));
        } 
        else 
        {
            echo ''; exit;
        }
    } else {
        require_once(dirname(dirname(__FILE__)) . "/engine/start.php");    
        $contents = elgg_view($view);
        header("Content-Length: " . strlen($contents));
    }

    $split_output = str_split($contents, 1024);

    foreach($split_output as $chunk)
        echo $chunk; 

?>
