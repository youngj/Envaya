<div class='padded'>
<?php
    $org = $vars['entity'];
?>
<form method='POST' action='/admin/new_featured'>
<?php echo view('input/securitytoken') ?>

<strong><a href='<?php echo $org->get_url() ?>'><?php echo escape($org->name) ?></a></strong>

<br /><br />
<div class='input'>
<label><?php echo __('featured:image'); ?></label>
<?php echo view('admin/featured_image', array(
    'name' => 'image_url',
    'org' => $org, 
    'value' => $org->get_icon('medium')
)); 
?>
</div>
<div class='input' style='clear:both'>
<label><?php echo __('featured:text'); ?></label>
<?php
    $homeWidget = $org->get_widget_by_name('home');
    
    echo view('input/tinymce',
        array(
            'name' => 'content',
            'valueIsHTML' => true,
            'value' => $homeWidget->content,
            'trackDirty' => true
        )
    );
?>
</div>
<?php

    echo view('input/submit', array('value' => __('publish')));

    echo view('input/hidden', array(
        'name' => 'username',
        'value' => $org->username
    ));
    
    
?>

</form>
</div>