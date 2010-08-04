<?php
    $entity = $vars['entity'];
    $org = $entity->getContainerEntity();
?>
<form method='POST' action='admin/save_featured'>
<?php echo view('input/securitytoken') ?>

<strong><a href='<?php echo $org->getURL() ?>'><?php echo escape($org->name) ?></a></strong>

<br /><br />

<div class='input'>
<label><?php echo __('featured:image'); ?></label>
<?php echo view('admin/featured_image', array(
    'internalname' => 'image_url',
    'org' => $org, 
    'value' => $entity->image_url
)); 
?>
</div>
<div class='input'>
<label><?php echo __('featured:text'); ?></label>
<?php

    echo view('input/tinymce',
        array(
            'internalname' => 'content',
            'valueIsHTML' => true,
            'allowCustomHTML' => true,
            'value' => $entity->content,
            'trackDirty' => true
        )
    );
    
?>
</div>
<?php

    echo view('input/submit',
        array('internalname' => 'submit',
            'class' => "submit_button",
            'trackDirty' => true,
            'value' => __('savechanges')));

    echo view('input/hidden', array(
        'internalname' => 'guid',
        'value' => $entity->guid
    ));
    
    
?>
</form>