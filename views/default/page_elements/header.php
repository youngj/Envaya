<?php

    /**
     * Elgg pageshell
     * The standard HTML header that displays across the site
     * 
     * @package Elgg
     * @subpackage Core
     * @author Curverider Ltd
     * @link http://elgg.org/
     * 
     * @uses $vars['config'] The site configuration settings, imported
     * @uses $vars['title'] The page title
     * @uses $vars['body'] The main content of the page
     * @uses $vars['messages'] A 2d array of various message registers, passed from system_messages()
     */
     
     // Set title
        if (empty($vars['title'])) {
            $title = $vars['config']->sitename;
        } else if (empty($vars['config']->sitename)) {
            $title = $vars['title'];
        } else {
            $title = $vars['config']->sitename . ": " . $vars['title'];
        }
        
        global $autofeed;
        if (isset($autofeed) && $autofeed == true) {
            $url = $url2 = full_url();
            if (substr_count($url,'?')) {
                $url .= "&view=rss";
            } else {
                $url .= "?view=rss";
            }
            if (substr_count($url2,'?')) {
                $url2 .= "&view=odd";
            } else {
                $url2 .= "?view=opendd";
            }
            $feedref = <<<END
            
    <link rel="alternate" type="application/rss+xml" title="RSS" href="{$url}" />
    <link rel="alternate" type="application/odd+xml" title="OpenDD" href="{$url2}" />
            
END;
        } else {
            $feedref = "";
        }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo escape($title); ?></title>

    <!-- include the default css file -->
    <link rel="stylesheet" href="<?php echo $vars['url']; ?>_css/css.css?lastcache=<?php echo $vars['config']->lastcache; ?>&viewtype=<?php echo $vars['view']; ?>" type="text/css" />
    
    <?php 
        echo $feedref;
        echo elgg_view('metatags',$vars); 
    ?>
</head>

<body>
