<?php
    $template = $vars['template'];
    ob_start();
?>
<div class='input'>
<label>From:</label>
<?php
    echo view('input/text',
        array(
            'name' => 'from',
            'value' => Config::get('contact:default_from'),
        )
    );   
?>
</div>

<div class='input'>
<label>Subject:</label>
<?php
    echo view('input/text',
        array(
            'name' => 'subject',
            'value' => '',
        )
    );   
?>
</div>
<div class='input'>
<?php
    echo view('admin/tinymce_email', array(
        'name' => 'content',
        'value' => '',
        'track_dirty' => true,
    ));
?>
</div>
<?php
    $content = ob_get_clean();
    echo view('admin/add_template', array(
        'action' => '/admin/contact/email/add',
        'template' => $template,
        'content' => $content,
    ));
?>