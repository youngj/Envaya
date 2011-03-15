<?php
    $widget = $vars['widget'];
    $relationship = $vars['relationship'];
    ob_start();
?>
<form method='POST' action='<?php echo $widget->get_edit_url() ?>?action=save_relationship'>
<div class='instructions'>
<?php 

echo sprintf(__('network:approve_instructions'), 
    escape($relationship->get_subject_name()), 
    escape(OrgRelationship::msg(OrgRelationship::get_reverse_type($relationship->type), 'header'))
);

echo sprintf(__('network:approve_instructions_2'), 
    escape($relationship->get_subject_name()), 
    escape($relationship->__('header')),
    __('network:approve')
);

 ?>
</div>

<?php

    echo view('widgets/network_edit_relationship_form', array('relationship' => $relationship));

    echo view('input/submit', array(
        'name' => '_save',
        'trackDirty' => true, 
        'value' => __('network:approve'),
    ));

?>
</form>

<?php
    $content = ob_get_clean();   
    echo view('section', array(
        'header' =>  $relationship->__('approve_header'), 
        'content' => $content
    ));
?>    