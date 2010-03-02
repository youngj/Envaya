<?php
?>
<form action="<?php echo $vars['url']; ?>action/org/edit" enctype="multipart/form-data" method="post">

<?php echo elgg_view('input/securitytoken'); ?>

<?php

    foreach($vars['config']->org_profile_fields as $shortname => $valtype) {

?>
	<p>
		<label>
			<?php echo elgg_echo("org:{$shortname}") ?><br />
			<?php echo elgg_view("input/{$valtype}",array(
                'internalname' => $shortname,
                'value' => preserve_input($shortname, $vars['entity']->$shortname),
                )); 
            ?>
		</label>
	</p>

<?php
		}
?>

<p>
    <label><?php echo elgg_echo("org:icon"); ?><br />
    <?php
        echo elgg_view("input/file",array('internalname' => 'icon'));
    ?>
    </label>
</p>
<p>
    <input type="hidden" name="org_guid" value="<?php echo $vars['entity']->getGUID(); ?>" />
    <input type="submit" class="submit_button" value="<?php echo elgg_echo("save"); ?>" />
</p>

</form>
