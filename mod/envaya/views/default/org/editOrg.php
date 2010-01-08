<?php
	/**
	 * Elgg Envaya plugin edit org view
	 *
	 * @package ElggGroups
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider Ltd
	 * @copyright Curverider Ltd 2008-2009
	 * @link http://elgg.com/
	 */

?>
<div class="contentWrapper">
<form action="<?php echo $vars['url']; ?>action/editOrg" enctype="multipart/form-data" method="post">

	<?php echo elgg_view('input/securitytoken'); ?>

	<p>
		<label><?php echo elgg_echo("org:icon"); ?><br />
		<?php

			echo elgg_view("input/file",array('internalname' => 'icon'));

		?>
		</label>
	</p>
<?php

	//var_export($vars['profile']);
	if (is_array($vars['config']->org_fields) && sizeof($vars['config']->org_fields) > 0)
		foreach($vars['config']->org_fields as $shortname => $valtype) {

?>

	<p>
		<label>
			<?php echo elgg_echo("org:{$shortname}") ?><br />
			<?php echo elgg_view("input/{$valtype}",array(
															'internalname' => $shortname,
															'value' => $vars['entity']->$shortname,
															)); ?>
		</label>
	</p>

<?php

		}

?>

	<p>
		<?php
			if ($vars['entity'])
			{
			?><input type="hidden" name="org_guid" value="<?php echo $vars['entity']->getGUID(); ?>" /><?php
			}
		?>
		<input type="hidden" name="user_guid" value="<?php echo page_owner_entity()->guid; ?>" />
		<input type="submit" class="submit_button" value="<?php echo elgg_echo("save"); ?>" />

	</p>

</form>
</div>

<div class="contentWrapper">
<div id="delete_group_option">
	<form action="<?php echo $vars['url'] . "action/deleteOrg"; ?>">
		<?php
			if ($vars['entity'])
			{
				$warning = elgg_echo("org:deletewarning");
			?>
			<input type="hidden" name="org_guid" value="<?php echo $vars['entity']->getGUID(); ?>" />
			<input type="submit" name="delete" value="<?php echo elgg_echo('org:delete'); ?>" onclick="javascript:return confirm('<?php echo $warning; ?>')"/><?php
			}
		?>
	</form>
</div><div class="clearfloat"></div>
</div>



