<?php
    $org = $vars['org'];

    ob_start();

    echo view('input/tinymce',
        array(
            'name' => 'blogbody',
            'id' => 'post_rich',
            'autoFocus' => true,
            'trackDirty' => true
        )
    );

    echo view('input/submit',
        array('name' => 'submit',
            'class' => "submit_button addUpdateButton",
            'trackDirty' => true,
            'value' => __('publish')));

    echo view('input/hidden', array(
        'name' => 'uuid',
        'value' => uniqid("",true)
    ));

    echo view('org/attachImage');

    $formBody = ob_get_clean();

    echo view('input/form', array(
        'id' => 'addPostForm',
        'action' => "{$org->get_url()}/post/new",
        'enctype' => "multipart/form-data",
        'body' => $formBody,
    ));
