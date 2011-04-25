<div class='section_content padded'>
<?php

    $widget = $vars['widget'];
    $org = $widget->get_container_entity();

ob_start();
?>

<div class='input'>
    <label><?php echo __('widget:contact:email:edit') ?></label><br />
<?php echo view("input/text", array('name' => 'email',
    'value' => $org->email,
    'js' => "style='width:250px'"
));

echo view("input/checkboxes", array('name' => 'public_email',
    'value' => $widget->get_metadata('public_email') ?: 'yes',
    'options' => array('yes' => __('show_website'))
));

?>
</div>

<div class='input'>
    <label><?php echo __('widget:contact:phone_number:edit') ?></label>
    <div class='help'><?php echo __('create:phone:help') ?></div>
    <div class='help'><?php echo __('create:phone:help_2') ?></div>
<?php echo view("input/text", array('name' => 'phone_number',
    'value' => $org->phone_number,
    'js' => "style='width:250px'"
));

echo view("input/checkboxes", array('name' => 'public_phone',
    'value' => $widget->get_metadata('public_phone') ?: 'yes',
    'options' => array('yes' => __('show_website'))
));


?>
</div>

<div class='input'>
    <label><?php echo __('widget:contact:street_address:edit') ?></label>
    <div class='help'><?php echo __('widget:contact:street_address:help') ?></div>
<?php echo view("input/longtext", array(
    'name' => 'street_address',
    'value' => $org->get_metadata('street_address'),
    'trackDirty' => true,
    'js' => 'style="height:50px"'
));
?>
</div>

<div class='input'>
    <label><?php echo __('widget:contact:mailing_address:edit') ?></label><br />
<?php echo view("input/longtext", array(
    'name' => 'mailing_address',
    'trackDirty' => true,
    'value' => $org->get_metadata('mailing_address'),
    'js' => 'style="height:50px"'
));
?>
</div>
<div class='input'>
    <label><?php echo __('widget:contact:name:edit') ?></label><br />
<?php echo view("input/text", array('name' => 'contact_name',
    'value' => $org->get_metadata('contact_name')
));
?>
</div>

<div class='input'>
    <label><?php echo __('widget:contact:title:edit') ?></label><br />
<?php echo view("input/text", array('name' => 'contact_title',
    'value' => $org->get_metadata('contact_title')
));
?>
</div>

<?php
    $content = ob_get_clean();

    echo view("widgets/edit_form", array(
        'widget' => $widget,
        'body' => $content
    ));


?>
</div>