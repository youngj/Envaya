<?php
    $value = $vars['value'];

    if (Session::isloggedin()) // hack to make it line up with tinymce content
    {
        echo "<div style='padding-top:28px'>";
    }
    
    echo "<div style='width:470px;height:365px;padding:4px;border:1px solid #ccc;overflow:auto'>";
    echo $value;
    echo "</div>";
    
    if (Session::isloggedin()) 
    {
        echo "</div>";
    }