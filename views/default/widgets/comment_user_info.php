<?php
    $name = @$vars['name'] ?: Session::get('user_name');
    $location = @$vars['location'] ?: Session::get('user_location');
    
    $user = Session::get_loggedin_user();
    if ($user && !$name)
    {
        $name = $user->name;
    }
?>
<table class='inputTable'>
<tr>
<th><?php echo __('message:name'); ?></th>
<td>
<?php 
    echo view('input/text', array('name' => 'name', 'value' => $name)); 
?>
</td>
</tr>
<tr>
<th><?php echo __('comment:location'); ?></th>
<td>
<?php 
    echo view('input/text', array('name' => 'location', 'value' => $location)); 
?>
</td>
</tr>
</table>
