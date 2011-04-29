<div id='heading'>
<?php        
    $logo = @$vars['logo'];
    $title = $vars['title'];    
    $escTitle = escape($title);    
    
    if ($logo)
    {
        $url = escape(@$vars['title_url']);
            
        echo "<h2><a href='$url'>".escape($vars['sitename'])."</a></h2>";
        if ($title)
        {
            echo "<h3>$escTitle</h3>";
        }        
    }
    else
    {
        echo "<h1>$escTitle</h1>";    
    }
?>
</div>