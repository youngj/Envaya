<?php

	/**
	 * Elgg engine bootstrapper
	 * Loads the various elements of the Elgg engine
	 * 
	 * @package Elgg
	 * @subpackage Core

	 * @author Curverider Ltd

	 * @link http://elgg.org/
	 */

	global $START_MICROTIME; 
	$START_MICROTIME = microtime(true);
    
    if (!include_once(dirname(__FILE__) . "/lib/exceptions.php")) {		// Exceptions 
        echo "Error in installation: could not load the Exceptions library.";
        exit;
    }
		
    if (!include_once(dirname(__FILE__) . "/lib/elgglib.php")) {		// Main Elgg library
        echo "Elgg could not load its main library.";
        exit;
    }
		
    if (!include_once(dirname(__FILE__) . "/lib/access.php")) {		// Access library
        echo "Error in installation: could not load the Access library.";
        exit;
    }

    if (!include_once(dirname(__FILE__) . "/lib/system_log.php")) {		// Logging library
        echo "Error in installation: could not load the System Log library.";
        exit;
    }

    if (!include_once(dirname(__FILE__) . "/lib/sessions.php")) {
        echo ("Error in installation: Elgg could not load the Sessions library");
        exit;
    }

    if (!include_once(dirname(__FILE__) . "/lib/languages.php")) {		// Languages library
        echo "Error in installation: could not load the languages library.";
        exit;
    }

    if (!include_once(dirname(__FILE__) . "/lib/input.php")) {		// Input library
        echo "Error in installation: could not load the input library.";
        exit;
    }

    if (!include_once(dirname(__FILE__) . "/lib/install.php")) {		// Installation library
        echo "Error in installation: could not load the installation library.";
        exit;
    }

    if (!include_once(dirname(__FILE__) . "/lib/cache.php")) {		// Installation library
        echo "Error in installation: could not load the cache library.";
        exit;
    }
        
    // Use fallback view until sanitised
    $oldview = get_input('view');
    set_input('view', 'failsafe');
		
	// Register the error handler
    set_error_handler('__elgg_php_error_handler');
    set_exception_handler('__elgg_php_exception_handler');
		
   if (!include_once(dirname(__FILE__) . "/settings.php"))  
        throw new InstallationException("Elgg could not load the settings file.");

    if (!include_once(dirname(__FILE__) . "/lib/database.php"))
        throw new InstallationException("Elgg could not load the main Elgg database library.");

    if (!include_once(dirname(__FILE__) . "/lib/actions.php")) {
        throw new InstallationException("Elgg could not load the Actions library");
    }	

    $file_exceptions = array('.','..','.DS_Store','Thumbs.db','.svn','CVS','cvs',
        'settings.php','settings.example.php','languages.php','exceptions.php','elgglib.php','access.php','database.php','actions.php','sessions.php'
    );

    $files = get_library_files(dirname(__FILE__) . "/lib",$file_exceptions);
    asort($files);

    global $CONFIG;

    foreach($files as $file) 
    {
        /*
        if (isset($CONFIG->debug) && $CONFIG->debug) 
            error_log("Loading $file..."); 
        */
        
        if (!include_once($file))
            throw new InstallationException("Could not load {$file}");
    }
	    
    trigger_elgg_event('boot', 'system');
			
	$installed = is_installed();
			
    if ($installed) 
    {
        load_plugins();
        trigger_elgg_event('plugins_boot', 'system');
    }
    
    if (!$installed && !substr_count($_SERVER["PHP_SELF"],"install.php") && !substr_count($_SERVER["PHP_SELF"],"css.php") && !substr_count($_SERVER["PHP_SELF"],"action_handler.php")) 
    {
	    header("Location: install.php");
	    exit;
	}
			
	if (!substr_count($_SERVER["PHP_SELF"],"install.php") && !substr_count($_SERVER["PHP_SELF"],"setup.php")) 
    {
        trigger_elgg_event('init', 'system');
    }
			
    set_input('view', $oldview);
    if (empty($oldview)) 
    {
	    if (empty($CONFIG->view)) 
	        $oldview = 'default';
        else
            $oldview = $CONFIG->view;
    }
    
    if ($installed && $db_installed) 
    {
        $lastupdate = datalist_get('simplecache_lastupdate');
        $lastcached = datalist_get('simplecache_'.$oldview);
        if ($lastupdate == 0 || $lastcached < $lastupdate) 
        {
            elgg_view_regenerate_simplecache();
            $lastcached = time();
            datalist_set('simplecache_lastupdate',$lastcached);
            datalist_set('simplecache_'.$oldview,$lastcached);
        }
        $CONFIG->lastcache = $lastcached;
    }    
    
?>