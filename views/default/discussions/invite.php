<div class='section_content padded'>
<?php
    $topic = $vars['topic'];
    $org = $topic->get_root_container_entity();
    
    $relationships = $org->query_relationships()
        ->where("subject_guid <> 0 OR subject_email <> ''")
        ->filter();
        
    $invited_emails = $topic->get_metadata('invited_emails') ?: array();
                
    $relationship_options = array();
    
    foreach ($relationships as $relationship)
    {
        $email = $relationship->get_subject_email();
        if ($email && !in_array($email, $invited_emails))
        {        
            $relationship_options[$email] = "{$relationship->get_subject_name()} ($email)";
        }
    }

?>

<form method='POST' action="<?php echo $topic->get_url() ?>/invite">
<?php echo view('input/securitytoken'); ?>
<div class='input'>
<label><?php echo strtr(__('discussions:select_partner_invite'), array('{topic}' => $topic->subject)); ?></label><br />
<?php
    if (sizeof($relationship_options))
    {
        echo view('input/checkboxes', array(
            'name' => 'invited_emails',
            'options' => $relationship_options
        ));
    }

    if (sizeof($invited_emails))
    {
        echo "<div style='font-style:italic;padding-top:5px'>".__('discussions:already_invited'). ": " . escape(implode(', ', $invited_emails))."</div>";
    }
    
    if (!sizeof($relationship_options))
    {
        $network = $org->get_widget_by_class('Network');
        echo "<a href='". $network->get_edit_url(). "?action=add_relationship&type=".OrgRelationship::Partnership."'>";
        echo __('network:add_partnership');
        echo "</a>";
        
    }
       
?>


</div>

<div class='input'>
<label><?php echo __('discussions:invite_message_label'); ?></label><br />
<?php

    echo view('input/longtext', array(
        'name' => 'invite_message',
        'trackDirty' => true,
        'value' => view('emails/discussion_invite', array('topic' => $topic))
    ));
?>
</div>

<?php
    echo view('input/submit', array('value' => __('discussions:send_invite')));
?>

</div>