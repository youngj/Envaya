<?php

    /**    
     * The standard HTML header that displays across the site     
     */

     // Set title

    echo view('page_elements/doctype');
    
    $lang = escape(Language::get_current_code());
        
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang ?>" lang="<?php echo $lang ?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo escape($vars['full_title']); ?></title>
    <base href='<?php echo Request::$protocol == 'https' ? Config::get('secure_url') : Config::get('url'); ?>' />     

    <?php
        echo view('page_elements/css', $vars);          
        if (PageContext::has_rss())
        {
            echo '<link rel="alternate" type="application/rss+xml" title="RSS" '.
                'href="'.escape(url_with_param(Request::full_original_url(), 'view', 'rss')).'" />';
        }   
        
        echo "<link rel='canonical' href='".escape(Request::canonical_url())."' />";
    ?>
    <link rel="shortcut icon" href="/_graphics/favicon2.ico" />
<script type='text/javascript'>
<?php echo view('js/header'); ?>
<?php 

if (PageContext::is_dirty())
{
    echo "setDirty(true);";
}

$js_strs = array();
foreach (PageContext::get_js_strings() as $key)
{
    $js_strs[$key] = __($key);
}
if (sizeof($js_strs))
{
    echo "var __ = ".json_encode($js_strs).";"; 
}
?>
</script>
    <?php echo PageContext::get_header_html(); ?>
</head>

<body class='<?php echo @$vars['bodyClass']; ?>'>

<?php if (get_input("__readonly") == "1") { ?>
<div style='position:absolute;background-color:white;width:600px;height:500px;left:0px;top:0px;opacity:0.01;z-index:100;filter:alpha(opacity=1);z-index:100'></div>
<?php } ?>