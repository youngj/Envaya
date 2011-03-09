<?php
    $report = $vars['report'];
    $input = @$vars['edit'] ? 'edit_input' : 'view_input';
    
    $field = $vars['field'];
    $view_args = $field->get_definition()->view_args;
    $constituencies = $view_args['constituencies'];
    
    //for ($activity_num = 1; $activity_num <= $args['num_activities']; $activity_num++)
    //{
?>
<table class='noBorderTable'>
<tr>
<td>&nbsp;</td>
<td style='border-right:1px solid #ccc;text-align:center'>&nbsp;</td>
<th style='border-right:1px solid #ccc;width:100px;text-align:center'>Direct Beneficiaries</th>
<th style='width:100px;text-align:center'>Indirect Beneficiaries</th>
</tr>

<?php foreach ($constituencies as $constituency) { ?>

<tr style='border-top:1px solid #ccc'>
<th rowspan='3' style='width:150px'><?php echo __("fcs:$constituency"); ?></th>
<td style='border-right:1px solid #ccc; padding-right:3px; text-align:right'><?php echo __('fcs:gender:male'); ?></td>
<td style='border-right:1px solid #ccc; text-align:center'><?php echo $report->get_field($constituency . "_male_direct")->$input(); ?></td>
<td style='text-align:center'><?php echo $report->get_field($constituency . "_male_indirect")->$input(); ?></td>
</tr>

<tr>
<td style='border-right:1px solid #ccc; padding-right:3px; text-align:right'><?php echo __('fcs:gender:female'); ?></td>
<td style='border-right:1px solid #ccc; text-align:center'><?php echo $report->get_field($constituency . "_female_direct")->$input(); ?></td>
<td style='text-align:center'><?php echo $report->get_field($constituency . "_female_indirect")->$input(); ?></td>
</tr>

<tr>
<td style='border-right:1px solid #ccc; padding-right:3px;  text-align:right'><?php echo __('fcs:total'); ?></td>
<td style='border-right:1px solid #ccc; text-align:center'><?php echo $report->get_field($constituency . "_direct")->$input(); ?></td>
<td style='text-align:center'><?php echo $report->get_field($constituency . "_indirect")->$input(); ?></td>
</tr>

<?php } ?>

</table>
<?php

?>