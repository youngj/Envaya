<?php

		/**
		 * Elgg plugins library
		 * Contains functions for managing plugins
		 * 
		 * @package Elgg
		 * @subpackage Core

		 * @author Curverider Ltd

		 * @link http://elgg.org/
		 */
				
		/**
		 * PluginException
		 *  
		 * A plugin Exception, thrown when an Exception occurs relating to the plugin mechanism. Subclass for specific plugin Exceptions.
		 * 
		 * @package Elgg
		 * @subpackage Exceptions
		 */
		class PluginException extends Exception {}
				
		/**
		 * For now, loads plugins directly
		 *
		 * @todo Add proper plugin handler that launches plugins in an admin-defined order and activates them on admin request
		 * @package Elgg
		 * @subpackage Core
		 */
		function load_plugins() {
			
			global $CONFIG;
            $plugins = $CONFIG->enabled_plugins;
				
            foreach($plugins as $mod) 
            {
                if (file_exists($CONFIG->pluginspath . $mod)) 
                {
                    if (!include($CONFIG->pluginspath . $mod . "/start.php"))
                        throw new PluginException(sprintf(elgg_echo('PluginException:MisconfiguredPlugin'), $mod));

                    if (is_dir($CONFIG->pluginspath . $mod . "/views")) 
                    {
                        if ($handle = opendir($CONFIG->pluginspath . $mod . "/views")) 
                        {
                            while ($viewtype = readdir($handle)) 
                            {
                                if (!in_array($viewtype,array('.','..','.svn','CVS')) && is_dir($CONFIG->pluginspath . $mod . "/views/" . $viewtype)) 
                                {
                                    autoregister_views("",$CONFIG->pluginspath . $mod . "/views/" . $viewtype,$CONFIG->pluginspath . $mod . "/views/", $viewtype);
                                }
                            }
                        }
                    }

                    if (is_dir($CONFIG->pluginspath . $mod . "/languages")) 
                    {
                        register_translations($CONFIG->pluginspath . $mod . "/languages/");
                    }
                }
            }
		}		

		/**
		 * Return whether a plugin is enabled or not.
		 *
		 * @param string $plugin The plugin name.
		 * @param int $site_guid The site id, if not specified then this is detected.
		 * @return bool
		 */
		function is_plugin_enabled($plugin, $site_guid = 0)
		{
			global $CONFIG;            
            return in_array($plugin, $CONFIG->enabled_plugins);
		}
?>