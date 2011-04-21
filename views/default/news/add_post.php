<div class='section_content padded'>
<?php
    $widget = $vars['widget'];

    ob_start();

    echo view('widgets/edit_initial_content', array('autoFocus' => true));
    
    echo view('input/submit',
        array(
            'class' => "submit_button addUpdateButton",
            'value' => __('publish')));

    echo view('input/hidden', array(
        'name' => 'uniqid',
        'value' => uniqid("",true)
    ));

    echo view('news/attach_image');

    $formBody = ob_get_clean();

    echo view('input/form', array(
        'id' => 'addPostForm',
        'action' => "{$widget->get_base_url()}/add",
        'enctype' => "multipart/form-data",
        'body' => $formBody,
    ));
?>
</div>