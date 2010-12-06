<?php

    $report = $vars['report'];
    $report_def = $report->get_report_definition();
    $submitter = $report->get_container_entity();
    $recipient = $report_def->get_container_entity();
?>

<html>
<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" bgcolor='#FFFFFF' style="font-size:13px;color:#000000;line-height:150%;font-family:trebuchet ms;">

<p>
<a href='<?php echo $submitter->get_url(); ?>'><?php echo escape($submitter->name); ?></a> has submitted
<?php echo escape($report->get_title()); ?>.
</p>

<p>
<?php echo escape($recipient->name); ?> should decide whether to approve this report, or send it back to <?php echo escape($submitter->name); ?> for further changes.
</p>

<p>
Log in to <b><a href='<?php echo $report->get_url() ?>/view_response'><?php echo $report->get_url() ?>/view_response</a></b> and choose either "<?php echo __('report:approve', 'en'); ?>" or "<?php echo __('report:set_draft', 'en'); ?>". 
</p>

<p>
If you click "<?php echo __('report:set_draft', 'en'); ?>", you will need to communicate with <?php echo escape($submitter->name); ?> to tell them what changes are necessary before their report is approved. They will be able to make changes to their report at <b><?php echo $report->get_url(); ?>/edit</b>.
</p>

<p>
The responses of <?php echo escape($submitter->name); ?> are included below:
</p>

<?php
    echo $report->render_view();
?>

</body>
</html>