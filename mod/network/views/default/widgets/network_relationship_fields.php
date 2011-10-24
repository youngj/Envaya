<?php
    $relationship = @$vars['relationship'];
?>
<tr><th><?php echo __('network:org_name'); ?></th>
<td><?php echo view('input/text', array(
    'name' => 'name', 
    'track_dirty' => true, 
    'id' => 'name', 
    'value' => $relationship ? $relationship->subject_name : '',
)); ?></td></tr>
<tr><th><span style='font-weight:normal'><?php echo __('phone_number'); ?></span></th>
<td><?php echo view('input/text', array(
    'name' => 'phone_number', 
    'track_dirty' => true, 
    'id' => 'phone_number', 
    'value' => $relationship ? $relationship->subject_phone : '',
)); ?></td></tr>
<tr><th><span style='font-weight:normal'><?php echo __('email'); ?></span></th>
<td><?php echo view('input/text', array(
    'name' => 'email', 
    'track_dirty' => true, 
    'id' => 'email', 
    'value' => $relationship ? $relationship->subject_email : '',
)); ?></td></tr>
<tr><th><span style='font-weight:normal'><?php echo __('website'); ?></span></th>
<td><?php echo view('input/text', array(
    'name' => 'website', 
    'track_dirty' => true, 
    'id' => 'website',
    'value' => $relationship ? $relationship->subject_website : '',
)); ?></td></tr>