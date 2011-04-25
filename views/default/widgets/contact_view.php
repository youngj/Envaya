<div class='section_content padded'>
<table class='contactTable'>
<?php
    $widget = $vars['widget'];
    $org = $widget->get_container_entity();

    if ($org->email && $widget->get_metadata('public_email') != 'no')
    {
        echo view('widgets/contact_field', array(
            'label' => __("widget:contact:email"), 
            'value' => view("output/email", array('value' => $org->email))
        ));
    }

    if ($org->phone_number && $widget->get_metadata('public_phone') != 'no')
    {
        echo view('widgets/contact_field', array(
            'label' => __("widget:contact:phone_number"), 
            'value' => escape($org->phone_number)
        ));    
    }

    $street_address = $org->get_metadata('street_address');
    if ($street_address)
    {
        echo view('widgets/contact_field', array(
            'label' => __("widget:contact:street_address"), 
            'value' => view("output/longtext", array('value' => $street_address))
        ));
    }

    $mailing_address = $org->get_metadata('mailing_address');
    if ($mailing_address)
    {
        echo view('widgets/contact_field', array(
            'label' => __("widget:contact:mailing_address"), 
            'value' => view("output/longtext", array('value' => $mailing_address))
        ));
    }

    $contact_name = $org->get_metadata('contact_name');
    if ($contact_name)
    {
        echo view('widgets/contact_field', array(
            'label' => __("widget:contact:name"), 
            'value' => escape($contact_name)
        ));    
    }

    $contact_title = $org->get_metadata('contact_title');
    if ($contact_title)
    {
        echo view('widgets/contact_field', array(
            'label' => __("widget:contact:title"), 
            'value' => escape($contact_title)
        ));
    }
?>
</table>
</div>