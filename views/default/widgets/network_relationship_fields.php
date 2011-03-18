<?php
    $relationship = @$vars['relationship'];
?>
<tr><th><?php echo __('network:org_name'); ?></th>
<td><?php echo view('input/text', array(
    'name' => 'name', 
    'trackDirty' => true, 
    'id' => 'name', 
    'value' => $relationship ? $relationship->subject_name : '',
)); ?></td></tr>
<tr><th><span style='font-weight:normal'><?php echo __('phone_number'); ?></span></th>
<td><?php echo view('input/text', array(
    'name' => 'phone_number', 
    'trackDirty' => true, 
    'id' => 'phone_number', 
    'value' => $relationship ? $relationship->subject_phone : '',
)); ?></td></tr>
<tr><th><span style='font-weight:normal'><?php echo __('email'); ?></span></th>
<td><?php echo view('input/text', array(
    'name' => 'email', 
    'trackDirty' => true, 
    'id' => 'email', 
    'value' => $relationship ? $relationship->subject_email : '',
)); ?></td></tr>
<tr><th><span style='font-weight:normal'><?php echo __('website'); ?></span></th>
<td><?php echo view('input/text', array(
    'name' => 'website', 
    'trackDirty' => true, 
    'id' => 'website',
    'value' => $relationship ? $relationship->subject_website : '',
)); ?></td></tr>