<div class='section_content padded'>
<?php

    $widget = $vars['widget'];
    $org = $widget->getContainerEntity();

ob_start();
?>

<div class='input'>
    <label><?php echo __('widget:contact:email:edit') ?></label><br />
<?php echo elgg_view("input/text", array('internalname' => 'email',
    'value' => $org->email,
    'js' => "style='width:250px'"
));

echo elgg_view("input/checkboxes", array('internalname' => 'public_email',
    'value' => $widget->public_email ?: 'yes',
    'options' => array('yes' => __('show_website'))
));

?>
</div>

<div class='input'>
    <label><?php echo __('widget:contact:phone_number:edit') ?></label>
    <div class='help'><?php echo __('create:phone:help') ?></div>
    <div class='help'><?php echo __('create:phone:help_2') ?></div>
<?php echo elgg_view("input/text", array('internalname' => 'phone_number',
    'value' => $org->phone_number,
    'js' => "style='width:250px'"
));

echo elgg_view("input/checkboxes", array('internalname' => 'public_phone',
    'value' => $widget->public_phone ?: 'yes',
    'options' => array('yes' => __('show_website'))
));


?>
</div>

<div class='input'>
    <label><?php echo __('widget:contact:street_address:edit') ?></label>
    <div class='help'><?php echo __('widget:contact:street_address:help') ?></div>
<?php echo elgg_view("input/longtext", array(
    'internalname' => 'street_address',
    'value' => $org->street_address,
    'trackDirty' => true,
    'js' => 'style="height:50px"'
));
?>
</div>

<div class='input'>
    <label><?php echo __('widget:contact:mailing_address:edit') ?></label><br />
<?php echo elgg_view("input/longtext", array(
    'internalname' => 'mailing_address',
    'trackDirty' => true,
    'value' => $org->mailing_address,
    'js' => 'style="height:50px"'
));
?>
</div>
<div class='input'>
    <label><?php echo __('widget:contact:name:edit') ?></label><br />
<?php echo elgg_view("input/text", array('internalname' => 'contact_name',
    'value' => $org->contact_name
));
?>
</div>

<div class='input'>
    <label><?php echo __('widget:contact:title:edit') ?></label><br />
<?php echo elgg_view("input/text", array('internalname' => 'contact_title',
    'value' => $org->contact_title
));
?>
</div>

<?php
    $content = ob_get_clean();

    echo elgg_view("widgets/edit_form", array(
        'widget' => $widget,
        'body' => $content
    ));


?>
</div>