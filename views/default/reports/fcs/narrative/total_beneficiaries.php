<?php
    $report = $vars['report'];
    $input = @$vars['edit'] ? 'edit_input' : 'view_input';
?>
<table class='paddedTable'>
<tr>
<td>&nbsp;</td>
<th style='text-align:center; width:120px; vertical-align:bottom'><?php echo __('fcs:directness:direct'); ?></th>
<th style='text-align:center; width:120px ; vertical-align:bottom'><?php echo __('fcs:directness:indirect'); ?></th>
</tr>
<tr>
<th style='text-align:right'><?php echo __('fcs:gender:female'); ?></th>
<td><?php echo $report->get_field('beneficiaries_female_direct')->$input(); ?></td>
<td><?php echo $report->get_field('beneficiaries_female_indirect')->$input(); ?></td>
</tr>
<tr>
<th style='text-align:right'><?php echo __('fcs:gender:male'); ?></th>
<td><?php echo $report->get_field('beneficiaries_male_direct')->$input(); ?></td>
<td><?php echo $report->get_field('beneficiaries_male_indirect')->$input(); ?></td>
</tr>
<tr>
<th style='text-align:right'><?php echo __('fcs:total'); ?></th>
<td><?php echo $report->get_field('beneficiaries_direct')->$input(); ?></td>
<td><?php echo $report->get_field('beneficiaries_indirect')->$input(); ?></td>
</tr>
</table>