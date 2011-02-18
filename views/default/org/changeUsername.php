<?php
    $org = $vars['org'];
?>
<div class='section_content padded'>
<form action='<?php echo $org->get_url() ?>/username/save' method='POST'>

<?php echo view('input/securitytoken') ?>
<div class='input'>
<label>
<?php echo __('username:current') ?>
</label><br />
<?php echo escape($org->username) ?>
</div>

<div class='input'>
<label>
<?php echo __('username:new') ?>
</label><br />
<?php echo view('input/text', array('value' => $org->username, 'name' => 'username')) ?>
</div>

<?php echo view('input/submit', array('value' => __('save'))) ?>

</form>
</div>