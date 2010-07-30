<?php
    $org = $vars['entity'];
?>
<form method='POST' action='admin/new_featured'>
<?php echo elgg_view('input/securitytoken') ?>

<strong><a href='<?php echo $org->getURL() ?>'><?php echo escape($org->name) ?></a></strong>

<br /><br />

<?php

    $homeWidget = $org->getWidgetByName('home');

    echo elgg_view('input/tinymce',
        array(
            'internalname' => 'content',
            'valueIsHTML' => true,
            'allowCustomHTML' => true,
            'value' => $homeWidget->content,
            'trackDirty' => true
        )
    );

    echo elgg_view('input/submit',
        array('internalname' => 'submit',
            'class' => "submit_button",
            'trackDirty' => true,
            'value' => __('publish')));

    echo elgg_view('input/hidden', array(
        'internalname' => 'username',
        'value' => $org->username
    ));
    
    
?>
</form>