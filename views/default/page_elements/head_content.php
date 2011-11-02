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
<script type='text/javascript'>
<?php    
    echo "var canonicalUrl=".json_encode(@$vars['canonical_url']).";\n";
    echo view('js/header');    
    echo "var jsStrs=".json_encode(PageContext::get_js_strings()).";"; 
    echo PageContext::get_inline_js();    
    echo "\n";
    if (PageContext::is_dirty())
    {
        echo "setDirty(true);";
    }    
?>
</script>
<?php echo PageContext::get_header_html(); ?>