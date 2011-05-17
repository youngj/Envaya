<?php
    $user = $vars['user'];
?>
<div class='section_content padded'>
<form action='<?php echo $user->get_url() ?>/username' method='POST'>

<?php echo view('input/securitytoken') ?>
<div class='input'>
<label>
<?php echo __('user:username:current') ?>:
</label><br />
<?php echo escape($user->username) ?>
</div>

<div class='input'>
<label>
<?php echo __('user:username:new') ?>:
</label><br />
<?php echo view('account/username_field', array(
    'min_length' => ($user instanceof Organization) ? 3 : 6, 
    'value' => $user->username
)); ?>
</div>
<?php echo view('focus', array('name' => 'username')) ?>
<?php echo view('input/submit', array('value' => __('savechanges'))) ?>

</form>
</div>