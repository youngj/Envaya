<?php
    $message = $vars['message'];
    
    echo "<p>";
    echo "@";
    
    $name = escape($message->from_name);
    if ($name)
    {
        echo $name;
    }    
    
    $location = escape($message->from_location);    
    if ($location)
    {
        echo " ($location)";
    }
    
    if (!$name && !$location)
    {
        echo $message->get_date_text();
    }
    
    echo ":&nbsp;";
    echo "<span></span>";
    echo "</p>";
    