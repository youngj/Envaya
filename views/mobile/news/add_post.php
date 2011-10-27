<?php
    echo view('news/add_post', $vars, 'default');
    
    $widget = $vars['widget'];
    $user = $widget->get_container_user();
?>
<br />
<a href='<?php echo $user->get_url() . "/addphotos" ?>?from=/pg/dashboard&t=<?php echo timestamp(); ?>'><?php echo __('upload:photos:title') ?></a>
