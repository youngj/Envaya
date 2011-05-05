<div id='heading'>
<?php
    $org = $vars['org'];
    $url = escape($org->get_url());
        
    echo "<h2><a href='$url'>".escape($org->name)."</a></h2>";
    
    if (@$vars['is_site_home'])
    {
        $tagline = $org->get_design_setting('tagline');
        if ($tagline)
        {
            echo "<h3>".escape($tagline)."</h3>";
        }        
    }
    else
    {
        echo "<h3>".escape($vars['title'])."</h3>";
    }
?>
</div>