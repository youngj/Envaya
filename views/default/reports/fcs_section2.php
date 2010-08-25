<?php

$report = $vars['report'];
$org = $report->get_container_entity();
$render = @$vars['edit'] ? 'edit' : 'view';

?>
<div class='input'>
<label><?php echo __('fcs:amount'); ?></label><br />
<?php
echo $report->get_field('amount')->$render(array(
    'input_type' => 'input/text'
));
?>
</div>