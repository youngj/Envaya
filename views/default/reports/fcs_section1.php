<?php

$report = $vars['report'];
$org = $report->get_container_entity();
$render = @$vars['edit'] ? 'edit' : 'view';

?>
<div class='input'>
<label><?php echo __('fcs:full_name'); ?></label><br />
<?php
echo $report->get_field('full_name')->$render(array(
    'input_type' => 'input/text',
    'default' => $org->name
));
?>
</div>