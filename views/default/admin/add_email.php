<?php
?>
<form method='POST' action='/admin/new_email'>
<?php echo view('input/securitytoken') ?>

<div class='input'>
<label>From:</label>
<?php
    echo view('input/text',
        array(
            'name' => 'from',
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
            'name' => 'subject',
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
            'name' => 'content',
            'valueIsHTML' => true,
            'value' => '',
            'trackDirty' => true
        )
    );
?>
</div>

<?php
    echo view('input/submit',
        array('name' => 'submit',
            'class' => "submit_button",
            'trackDirty' => true,
            'value' => __('save')));
?>
</form>