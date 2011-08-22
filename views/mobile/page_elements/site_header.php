<div id='heading'>
<?php
    $url = escape($vars['site_url']);
        
    echo "<h2><a href='$url'>".escape($vars['site_name'])."</a></h2>";
    
    if (@$vars['is_site_home'])
    {
        if ($vars['tagline'])
        {
            echo "<h3>".escape($vars['tagline'])."</h3>";
        }        
    }
    else
    {
        echo "<h3>".escape($vars['title'])."</h3>";
    }
?>
</div>