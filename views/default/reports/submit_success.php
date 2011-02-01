<?php
    $report = $vars['report'];   
    $report_def = $report->get_report_definition();
    $sent_email = $vars['sent_email'];
?>

<div class='section_content padded'>    
<p>
<strong><?php echo __('report:submit_success_2'); ?></strong>
</p>

<p>
<?php echo sprintf(__("report:submit_notified"), escape($report_def->get_container_entity()->name)); ?>
</p>

<?php 
if ($sent_email) {
?>
<p>
<?php echo sprintf(__("report:email_confirmation_sent"), "<em>".escape($report->get_container_entity()->email)."</em>"); ?>
</p>
<?php
}
?>

<p>
<?php echo sprintf(__("report:if_changes_required"), escape($report_def->get_container_entity()->name)); ?>
</p>

<p>
<?php echo sprintf(__("report:if_feedback"), "<a href='/envaya/contact'>".__('report:send_feedback_message')."</a>"); ?>
</p>

<ul>
<li>
<strong><a href='<?php echo $report->get_container_entity()->get_url(); ?>'><?php echo __('dashboard:view_home'); ?></a></strong>
</li>
<li>
<strong><a href='<?php echo $report->get_container_entity()->get_url(); ?>/dashboard'><?php echo __('report:edit_website'); ?></a></strong>
</li>
</ul>

</div>   
