<?php
    echo view('org/addPost', $vars, 'default');
    
    $org = $vars['org'];
?>
    <br />
    <a href='<?php echo $org->get_url() . "/addphotos" ?>?from=pg/dashboard&t=<?php echo time(); ?>'><?php echo __('addphotos:title') ?></a>
