<?php

	$org = $vars['entity'];

?>

<form action="<?php echo $vars['url']; ?>action/changeEmail" enctype="multipart/form-data" method="post">
    <?php echo elgg_view('input/securitytoken'); ?>
	<label>
		<?php echo elgg_echo("org:postemail"); ?>
	</label><br />
	<?php echo $org->getPostEmail(); ?>
    <input type="hidden" name="org_guid" value="<?php echo $org->guid; ?>" />
    <input type="submit" class="submit_button" value="<?php echo elgg_echo("org:changeemail"); ?>" />
</form>
