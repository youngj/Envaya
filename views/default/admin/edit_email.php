<?php
    $email = $vars['email'];
?>
<form method='POST' action='admin/save_email'>
<?php echo view('input/securitytoken') ?>

<div class='input'>
<label>From:</label>
<?php
    echo view('input/text',
        array(
            'internalname' => 'from',
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
            'internalname' => 'subject',
            'value' => $email->subject,
        )
    );   
?>
</div>
<div class='input'>
<label>Message:</label>
<?php

    echo view('input/tinymce',
        array(
            'internalname' => 'content',
            'valueIsHTML' => true,
            'allowCustomHTML' => true,
            'value' => $email->content,
            'trackDirty' => true
        )
    );
?>
</div>

<div class='input'>
<label><?php echo __('language'); ?></label>
<?php

    echo view("input/pulldown", array(
        'internalname' => 'language', 
        'value' => $email->getLanguage(), 
        'options_values' => Language::get_options()));
?>
</div>

<?php
    echo view('input/hidden',
        array('internalname' => 'email',
            'value' => $email->guid));

    echo view('input/alt_submit', array(
        'internalname' => "delete",
        'internalid' => 'widget_delete',
        'trackDirty' => true,
        'confirmMessage' => __('areyousure'),
        'value' => __('delete')
    ));
            
    echo view('input/submit',
        array('internalname' => 'submit',
            'class' => "submit_button",
            'trackDirty' => true,
            'value' => __('save')));
?>
</form>