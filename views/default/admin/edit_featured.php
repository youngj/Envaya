<?php
    $entity = $vars['entity'];
    $org = $entity->getContainerEntity();
?>
<form method='POST' action='admin/save_featured'>
<?php echo elgg_view('input/securitytoken') ?>

<strong><a href='<?php echo $org->getURL() ?>'><?php echo escape($org->name) ?></a></strong>

<br /><br />

<?php

    echo elgg_view('input/tinymce',
        array(
            'internalname' => 'content',
            'valueIsHTML' => true,
            'allowCustomHTML' => true,
            'value' => $entity->content,
            'trackDirty' => true
        )
    );

    echo elgg_view('input/submit',
        array('internalname' => 'submit',
            'class' => "submit_button",
            'trackDirty' => true,
            'value' => __('savechanges')));

    echo elgg_view('input/hidden', array(
        'internalname' => 'guid',
        'value' => $entity->guid
    ));
    
    
?>
</form>