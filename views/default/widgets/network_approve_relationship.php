<?php
    $widget = $vars['widget'];
    $relationship = $vars['relationship'];
    ob_start();
?>
<form method='POST' action='<?php echo $widget->get_edit_url() ?>?action=edit_relationship'>
<?php
echo view('input/securitytoken');
echo view('input/hidden', array('name' => 'guid', 'value' => $relationship->guid));
?>
<div class='instructions'>
<p>
<?php 


echo strtr(__('network:approve_instructions'), array(
    '{name}' => "<a target='_blank' href='".escape($relationship->get_subject_url())."'>".escape($relationship->get_subject_name()).'</a>', 
    '{type}' => escape(OrgRelationship::msg_for_type(OrgRelationship::get_reverse_type($relationship->type), 'header'))
));
echo ' ';
echo strtr(__('network:approve_instructions_2'), array(
    '{name}' => escape($relationship->get_subject_name()), 
    '{type}' => escape($relationship->msg('header')),
    '{button}' => __('network:add_button')
));
 ?>
</p>
</div>

<?php

    echo "<div><em>".sprintf(__('network:describe_relationship'), escape($relationship->get_subject_name()))."</em></div>";
    echo view('input/tinymce', array('name' => 'content', 'value' => $relationship->content, 'trackDirty' => true));

    echo view('input/alt_submit', array(
        'name' => "delete_relationship",
        'id' => 'widget_delete',
        'trackDirty' => true,
        'value' => __('network:dont_add_button')
    ));

    echo view('input/submit', array(
        'name' => '_save',
        'trackDirty' => true, 
        'value' => __('network:add_button'),
    ));

?>
</form>

<?php
    $content = ob_get_clean();   
    echo view('section', array(
        'header' =>  $relationship->msg('add_header'), 
        'content' => $content
    ));
?>    