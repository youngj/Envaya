<div class='padded'>
<?php
?>
<form method='POST' action='/admin/add_email'>
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
<?php
    echo view('admin/tinymce_email', array(
        'name' => 'content',
        'value' => '',
        'trackDirty' => true,
    ));
?>
</div>

<?php
    echo view('input/submit', array('value' => __('save')));
?>
</form>
</div>