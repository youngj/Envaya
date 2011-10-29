<?php
    $user = $vars['user'];
?>
<a href='<?php echo $user->get_url() ?>'><?php echo __('dashboard:view_home') ?></a>
<br />
<a href='<?php echo $user->username ?>/settings'><?php echo __('dashboard:settings') ?></a>
<br />
<a href='<?php echo $user->get_url() ?>/help'><?php echo __('help') ?></a>
