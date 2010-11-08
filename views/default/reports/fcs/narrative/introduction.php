<?php

$report = $vars['report'];
$org = $report->get_container_entity();

?>  
<?php if (@$vars['edit'] && false) { ?>
<div class='instructions'>
<p><?php echo __('fcs:narrative:preamble'); ?></p>
<p><?php echo sprintf(__('report:youare'), escape($org->name));?></p>
<p><?php echo __('report:instructions'); ?></p>
</div>
<?php } ?>

<?php echo view('reports/default_section', $vars); ?>
