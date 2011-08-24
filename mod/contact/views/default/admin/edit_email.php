<div class='padded'>
<?php
    $email = $vars['email'];
?>

<form method='POST' action='<?php echo $email->get_url(); ?>/edit'>
<?php echo view('input/securitytoken') ?>

<?php
    echo view('admin/email_filters_input', array('email' => $email));
?>

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
<script type='text/javascript'>
<?php echo view('js/save_draft', array('guid' => $email->guid)); ?>
</script>
<div class='input'>
<?php
    echo view('admin/tinymce_email', array(
        'name' => 'content',
        'value' => $email->content,
        'track_dirty' => true,
        'saveDraft' => true,
        'entity' => $email,        
    ));
?>
</div>
<?php
    echo view('input/alt_submit', array(
        'name' => "delete",
        'id' => 'widget_delete',
        'track_dirty' => true,
        'confirm' => __('areyousure'),
        'value' => __('delete')
    ));
            
    echo view('input/submit', array(
        //'attrs' => array('onclick' => 'updateFilterJson(); return true;'),
        'value' => __('save')));
?>
</form>
</div>