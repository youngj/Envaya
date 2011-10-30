<?php
    $widget = $vars['widget'];
    $user = $widget->get_container_user();
?>
<div class='section_content padded'>

<table class='contactTable'>
<tr>
<th><?php echo __('username'); ?>:</th><td><?php echo escape($user->username); ?></td>
</tr>
<tr>
<th><?php echo __('user:registration_date'); ?>:</th><td><?php 
    echo get_date_text($user->time_created); 
?></td>
</tr>

</table>
</div>