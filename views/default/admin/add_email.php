<?php
?>
<form method='POST' action='admin/new_email'>
<?php echo view('input/securitytoken') ?>

<div class='input'>
<label>From:</label>
<?php
    echo view('input/text',
        array(
            'internalname' => 'from',
            'value' => 'Envaya',
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
            'value' => '',
        )
    );   
?>
</div>
<div class='input'>
<label>Body:</label>
<?php

    echo view('input/tinymce',
        array(
            'internalname' => 'content',
            'valueIsHTML' => true,
            'allowCustomHTML' => true,
            'value' => '',
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
        'value' => get_language(), 
        'options_values' => Language::get_options()));
?>
</div>

<?php
    echo view('input/submit',
        array('internalname' => 'submit',
            'class' => "submit_button",
            'trackDirty' => true,
            'value' => __('save')));
?>
</form>