<?php
    echo view('news/add_post', $vars, 'default');
    
    $widget = $vars['widget'];
    $org = $widget->get_root_container_entity();
?>
<br />
<a href='<?php echo $org->get_url() . "/addphotos" ?>?from=/pg/dashboard&t=<?php echo time(); ?>'><?php echo __('upload:photos:title') ?></a>
