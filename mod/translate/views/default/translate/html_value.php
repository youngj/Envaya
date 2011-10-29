<?php
    $value = $vars['value'];

    if (Session::is_logged_in()) // hack to make it line up with tinymce content
    {
        echo "<div style='padding-top:28px'>";
    }
    
    echo "<div style='width:488px;height:365px;padding:4px;border:1px solid #ccc;overflow:auto'>";
    echo $value;
    echo "</div>";
    
    if (Session::is_logged_in()) 
    {
        echo "</div>";
    }