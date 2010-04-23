<p>
<?php echo sprintf(elgg_echo('feedback:instructions'), "<a href='mailto:info@envaya.org'>info@envaya.org</a>") ?>
</p>
<form method="POST" action='action/sendFeedback'>
<div class='input'>
<label><?php echo elgg_echo('feedback:message') ?>:</label>
<?php 
    echo elgg_view('input/longtext', array(
        'internalname' => 'message'
    ));
?>
</div>
<div class='input'>
<label><?php echo elgg_echo('feedback:name') ?>:</label>
<?php 
    echo elgg_view('input/text', array(
        'internalname' => 'name'
    ));
?>
</div>
<div class='input'>
<label><?php echo elgg_echo('feedback:email') ?>:</label>
<?php 
    echo elgg_view('input/text', array(
        'internalname' => 'email'
    ));
?>
</div>

<?php 
    echo elgg_view('input/submit', array(
        'internalname' => 'submit',
        'value' => elgg_echo('feedback:send'),
    ));
?>

</form>