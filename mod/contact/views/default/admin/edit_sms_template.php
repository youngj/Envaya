<?php
    $template = $vars['template'];
    ob_start();
?>
<div class='input'>
<?php
    echo view('admin/sms_textarea', array(
        'name' => 'content',
        'value' => $template->content,
        'track_dirty' => true,
    ));
?>
</div>
<?php   
    $content = ob_get_clean();
    echo view('admin/edit_template', array(
        'template' => $template, 
        'content' => $content
    ));