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
            'value' => $template->from,
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
            'value' => $template->subject,
        )
    );   
?>
</div>
<?php echo view('js/save_draft', array('guid' => $template->guid)); ?>
<div class='input'>
<?php
    echo view('admin/tinymce_email', array(
        'name' => 'content',
        'value' => $template->content,
        'track_dirty' => true,
        'saveDraft' => true,
        'entity' => $template,        
    ));
?>
</div>
<?php   
    $content = ob_get_clean();
    echo view('admin/edit_template', array(
        'template' => $template, 
        'content' => $content
    ));