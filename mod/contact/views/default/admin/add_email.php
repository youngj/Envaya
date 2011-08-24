<div class='padded'>
<?php
?>
<form method='POST' action='/admin/contact/email/add'>
<?php echo view('input/securitytoken') ?>

<?php
    echo view('admin/email_filters_input');
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
    echo view('input/submit', array('value' => __('save')));
?>
</form>
</div>