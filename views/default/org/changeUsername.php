<?php
    $org = $vars['org'];
?>
<div class='section_content padded'>
<form action='<?php echo $org->getURL() ?>/username/save' method='POST'>

<?php echo elgg_view('input/securitytoken') ?>
<div class='input'>
<label>
<?php echo elgg_echo('username:current') ?>
</label><br />
<?php echo escape($org->username) ?>
</div>

<div class='input'>
<label>
<?php echo elgg_echo('username:new') ?>
</label><br />
<?php echo elgg_view('input/text', array('value' => $org->username, 'internalname' => 'username')) ?>
</div>

<?php echo elgg_view('input/submit', array('value' => elgg_echo('save'))) ?>

</form>
</div>