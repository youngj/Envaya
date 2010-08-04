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

        $cacheVersion = $vars['config']->cache_version;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo escape($title); ?></title>

    <base href='<?php echo $vars['url'] ?>' />

    <?php
        echo '<link rel="stylesheet" href="_css/'.escape(PageContext::get_theme()).'.css?v='.$cacheVersion.'" type="text/css" />';
    ?>

    <!--[if IE 6]>
    <style type='text/css'>
    #site_menu a,
    #edit_pages_menu a { width:10px; }
    </style>
    <![endif]-->


<script type='text/javascript'>
<?php echo view('js/header'); ?>
</script>

</head>

<body class='<?php echo @$vars['bodyClass']; ?>'>

<?php if (get_input("__readonly") == "1") { ?>
<div style='position:absolute;background-color:white;width:600px;height:500px;left:0px;top:0px;opacity:0.01;z-index:100;filter:alpha(opacity=1);z-index:100'></div>
<?php } ?>