<?php
    $org = $vars['org'];
?>
<a href='<?php echo $org->get_url() ?>'><?php echo __('dashboard:view_home') ?></a>
<br />
<a href='<?php echo $org->username ?>/settings'><?php echo __('dashboard:settings') ?></a>
<br />
<a href='<?php echo $org->get_url() ?>/help'><?php echo __('help') ?></a>
