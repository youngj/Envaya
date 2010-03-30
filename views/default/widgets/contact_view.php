<div class='padded'>
<table class='contactTable'>
<?php

    $widget = $vars['widget'];
    $org = $widget->getContainerEntity();

    function contact_field($label, $value)
    {
        echo "<tr><th>$label</th><td>$value</td></tr>";
    }    

    if ($widget->public_email == 'yes')
    {           
        echo contact_field(elgg_echo("widget:contact:email"), elgg_view("output/email", array('value' => $org->email)));
    }
    
    if ($org->phone_number)
    {   
        echo contact_field(elgg_echo("widget:contact:phone_number"), elgg_view("output/text", array('value' => $org->phone_number)));
    }
    
    if ($org->street_address)
    {   
        echo contact_field(elgg_echo("widget:contact:street_address"), elgg_view("output/longtext", array('value' => $org->street_address)));
    }    

    if ($org->mailing_address)
    {   
        echo contact_field(elgg_echo("widget:contact:mailing_address"), elgg_view("output/longtext", array('value' => $org->mailing_address)));
    }    


    if ($org->contact_name)
    {           
        echo contact_field(elgg_echo("widget:contact:name"), elgg_view("output/text", array('value' => $org->contact_name)));
    }

    if ($org->contact_title)
    {           
        echo contact_field(elgg_echo("widget:contact:title"), elgg_view("output/text", array('value' => $org->contact_title)));
    }

?>
</table>
</div>