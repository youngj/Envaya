<?php
    echo view('page_elements/doctype');
    $lang = escape(Language::get_current_code());
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang ?>" lang="<?php echo $lang ?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo escape(@$vars['full_title']); ?></title>    
    <?php
        if (@$vars['base_url'])
        {
            echo "<base href='".escape($vars['base_url'])."' />";
        }    
        echo view('page_elements/css', $vars);          
        if (@$vars['rss_url'])
        {
            echo "<link rel='alternate' type='application/rss+xml' title='RSS' href='".escape($vars['rss_url'])."' />";
        }
        if (@$vars['canonical_url'])
        {
            echo "<link rel='canonical' href='".escape($vars['canonical_url'])."' />";
        }
    ?>
<link rel="shortcut icon" href="/_graphics/favicon2.ico" />
<script type='text/javascript'>
<?php echo view('js/header'); ?>
<?php 
echo "var canonicalUrl=".json_encode(@$vars['canonical_url']).";";
if (PageContext::is_dirty())
{
    echo "setDirty(true);";
}
echo "var js_strs=".json_encode(PageContext::get_js_strings()).";"; 
?>
</script>
    <?php echo PageContext::get_header_html(); ?>
</head>
<body>
<?php if (get_input("__readonly") == "1") { ?>
<div style='position:absolute;background-color:white;width:600px;height:500px;left:0px;top:0px;opacity:0.01;z-index:100;filter:alpha(opacity=1);z-index:100'></div>
<?php } ?>