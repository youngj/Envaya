<div class='padded'>
<?php
    $email = $vars['email'];
?>
<form method='POST' action='/admin/edit_email'>
<?php echo view('input/securitytoken') ?>

<div class='input'>
<label>From:</label>
<?php
    echo view('input/text',
        array(
            'name' => 'from',
            'value' => $email->from,
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
            'value' => $email->subject,
        )
    );   
?>
</div>
<div class='input'>
<label>Message:</label>
<?php
    echo view('admin/tinymce_email', array(
        'name' => 'content',
        'value' => $email->content,
    ));
?>
</div>
<?php
    echo view('input/hidden',
        array('name' => 'email',
            'value' => $email->guid));

    echo view('input/alt_submit', array(
        'name' => "delete",
        'id' => 'widget_delete',
        'trackDirty' => true,
        'confirm' => __('areyousure'),
        'value' => __('delete')
    ));
            
    echo view('input/submit', array('value' => __('save')));
?>
</form>
</div>