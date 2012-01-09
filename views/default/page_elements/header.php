<?php            
    $class = @$vars['org_only'] ? "org_only_heading" : "";

    echo "<div id='heading'>";
    echo "<h1 class='$class'>";
    
    $title = @$vars['title'] ?: '';    
    
    if (isset($vars['header_breadcrumb_items']))
    {
        echo view('breadcrumb', array(
            'items' => $vars['header_breadcrumb_items'],
        ));
    }
    else
    {    
        echo escape($title);    
    }
    
    echo "</h1>";    
    echo "</div>";