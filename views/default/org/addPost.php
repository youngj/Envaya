<?php
    $org = $vars['org'];

    ob_start();

    echo view('input/tinymce',
        array(
            'internalname' => 'blogbody',
            'internalid' => 'post_rich',
            'trackDirty' => true
        )
    );

    echo view('input/submit',
        array('internalname' => 'submit',
            'class' => "submit_button addUpdateButton",
            'trackDirty' => true,
            'value' => __('publish')));

    echo view('input/hidden', array(
        'internalname' => 'uuid',
        'value' => uniqid("",true)
    ));

    ?>

<script type='text/javascript'>

function showAttachImage($show)
{
    $dirty = window.dirty;
    setDirty(false);
    setTimeout(function() { setDirty($dirty) }, 5);

    if (!window.tinyMCE)
    {
        return;
    }

    setTimeout(function() {
        tinyMCE.activeEditor.execCommand("mceImage");
    }, 1);
}
</script>

<div id='attachControls'>
    <a href='javascript:void(0)' onclick='showAttachImage()'><img src='_graphics/attach_image.gif?v2' /></a>
    <a href='javascript:void(0)' onclick='showAttachImage()'><?php echo __('dashboard:attach_image') ?></a>
</div>

    <?php

    $formBody = ob_get_clean();

    echo view('input/form', array(
        'internalid' => 'addPostForm',
        'action' => "{$org->get_url()}/post/new",
        'enctype' => "multipart/form-data",
        'body' => $formBody,
    ));
