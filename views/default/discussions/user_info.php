<table class='inputTable'>
<tr>
<th><?php echo __('comment:name'); ?></th>
<td>
<?php 
    $name = Session::get('user_name');
    echo view('input/text', array('name' => 'name', 'value' => $name)); 
?>
</td>
</tr>
<tr>
<th><?php echo __('comment:location'); ?></th>
<td>
<?php 
    $location = Session::get('user_location');
    echo view('input/text', array('name' => 'location', 'value' => $location)); 
?>
</td>
</tr>
</table>
