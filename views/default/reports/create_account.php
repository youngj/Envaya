<?php
    $report_def = $vars['report_def'];
?>

<div class='padded'>
<div id='instructions'>
<p>
    <?php echo __('report:create_account_instructions') ?>
</p>
<p>
    <?php echo __('report:create_account_instructions_2') ?>
</p>

</div>

<form action='<?php echo secure_url($report_def->get_url()."/new_account") ?>' method='POST'>
<?php
    echo view('org/create_account_form');
?>
</form>

</div>