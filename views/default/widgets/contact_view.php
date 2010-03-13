<div class='padded'>
<?php

    $widget = $vars['widget'];
    $org = $widget->getContainerEntity();

    function contact_field($label, $value)
    {
        echo "<div style='padding:4px'>$label $value</div>";
    }

    if ($org->contact_name)
    {           
        echo contact_field(elgg_echo("widget:contact:name"), elgg_view("output/text", array('value' => $org->contact_name)));
    }

    if ($org->contact_title)
    {           
        echo contact_field(elgg_echo("widget:contact:title"), elgg_view("output/text", array('value' => $org->contact_title)));
    }

    if ($widget->public_email == 'yes')
    {           
        echo contact_field(elgg_echo("widget:contact:email"), elgg_view("output/email", array('value' => $org->email)));
    }
    
    if ($org->phone_number)
    {   
        echo contact_field(elgg_echo("widget:contact:phone_number"), elgg_view("output/text", array('value' => $org->phone_number)));
    }
    
?>
</div>