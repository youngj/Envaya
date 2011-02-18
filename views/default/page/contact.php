<div class='section_content padded'>

<?php echo $vars['widget']->render_content(); ?>

<form method="POST" action='/pg/send_feedback'>
<div class='input'>
<div><label><?php echo __('feedback:message') ?>:</label></div>
<?php
    echo view('input/longtext', array(
        'name' => 'message'
    ));
?>
</div>
<div class='input'>
<label><?php echo __('feedback:name') ?>:</label>
<?php
    echo view('input/text', array(
        'name' => 'name'
    ));
?>
</div>
<div class='input'>
<label><?php echo __('feedback:email') ?>:</label>
<?php
    echo view('input/text', array(
        'name' => 'email'
    ));
?>
</div>

<?php
    echo view('input/submit', array(
        'name' => 'submit',
        'value' => __('feedback:send'),
    ));
?>

</form>
</div>