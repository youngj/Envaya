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

    echo view('org/attachImage');

    $formBody = ob_get_clean();

    echo view('input/form', array(
        'internalid' => 'addPostForm',
        'action' => "{$org->get_url()}/post/new",
        'enctype' => "multipart/form-data",
        'body' => $formBody,
    ));
