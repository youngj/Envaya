<?php

    /**    
     * The standard HTML header that displays across the site
     
     * @uses $vars['config'] The site configuration settings, imported
     * @uses $vars['title'] The page title
     * @uses $vars['body'] The main content of the page
     */

     // Set title
        if (empty($vars['title'])) {
            $title = $vars['config']->sitename;
        } else if (empty($vars['config']->sitename)) {
            $title = $vars['title'];
        } else {
            $title = $vars['config']->sitename . ": " . $vars['title'];
        }

    echo view('page_elements/doctype');
        
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo escape($title); ?></title>
    <base href='<?php echo $vars['config']->url; ?>' />     

    <?php
        echo view('page_elements/css', $vars);          
        if (PageContext::has_rss())
        {
            echo '<link rel="alternate" type="application/rss+xml" title="RSS" '.
                'href="'.url_with_param(Request::full_original_url(), 'view', 'rss').'" />';
        }   
    ?>
    <link rel="shortcut icon" href="/_graphics/favicon2.ico" />
<script type='text/javascript'>
<?php echo view('js/header'); ?>
</script>
</head>

<body class='<?php echo @$vars['bodyClass']; ?>'>

<?php if (get_input("__readonly") == "1") { ?>
<div style='position:absolute;background-color:white;width:600px;height:500px;left:0px;top:0px;opacity:0.01;z-index:100;filter:alpha(opacity=1);z-index:100'></div>
<?php } ?>