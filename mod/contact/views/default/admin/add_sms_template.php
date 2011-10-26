<?php
    $template = $vars['template'];
    ob_start();
?>
<div class='input'>
<?php
    echo view('admin/sms_textarea', array(
        'name' => 'content',
        'value' => '',
        'track_dirty' => true,
    ));
?>
</div>
<?php
    $content = ob_get_clean();
    echo view('admin/add_template', array(
        'action' => '/admin/contact/sms/add',
        'template' => $template,
        'content' => $content,
    ));
?>