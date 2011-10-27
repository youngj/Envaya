<?php
    $name = @$vars['name'] ?: Session::get('user_name');
    $location = @$vars['location'] ?: Session::get('user_location');
    $email = @$vars['email'] ?: Session::get('user_email');
    
    $user = Session::get_loggedin_user();
    if ($user)
    {
        if (!$name)
        {
            $name = $user->name;
        }
        
        if (!$email)
        {
            $email = $user->email;
        }
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

<tr>
<th style='vertical-align:top;padding-top:top'><?php echo __('comment:email'); ?>
<div style='font-weight:normal;font-size:11px'><?php echo __('comment:optional'); ?></div>
</th>
<td>
<?php 
    echo view('input/text', array('name' => 'email', 'value' => $email)); 
?>
<div class='help' style='font-size:11px'><?php echo __('comment:email_help'); ?></div>
</td>
</tr>
</table>
