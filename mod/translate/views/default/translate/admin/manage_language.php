<?php
    $language = $vars['language'];
?>
<div class='section_content padded'>
<form method='POST' action='/tr/admin/<?php echo escape($language->code); ?>'>
<?php
    echo view('input/securitytoken');    
?>
<div class='input'>
<label><?php echo __('itrans:language_code'); ?></label><br />
<?php echo escape($language->code); ?>
</div>
<div class='input'>
<label><?php echo __('itrans:language_name'); ?></label><br />
<?php 
    echo view('input/text', array('name' => 'name', 'value' => $language->name));
?>
</div>

<div class='input'>
<label><?php echo __('itrans:language_groups'); ?></label><br />
<?php 
    $groups = $language->get_available_groups();
    
    $group_options = array();
    $group_value = array();
    foreach ($groups as $group)
    {
        $group_options[$group->name] = $group->name;
        if ($group->is_enabled())
        {
            $group_value[] = $group->name;
        }
    }
    
    echo view('input/checkboxes', array(
        'name' => 'group_names',
        'columns' => 3,
        'options' => $group_options,
        'value' => $group_value
    ));
?>
</div>

<?php
    echo view('input/alt_submit', array('name' => 'delete', 'id' => 'widget_delete', 'value' => __('delete')));
    echo view('input/submit', array('value' => __('savechanges')));
?>
</div>