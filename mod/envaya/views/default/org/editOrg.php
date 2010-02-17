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
<form action="<?php echo $vars['url']; ?>action/editOrg" enctype="multipart/form-data" method="post">

	<?php echo elgg_view('input/securitytoken'); ?>

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
        <label><?php echo elgg_echo("org:icon"); ?><br />
        <?php

            echo elgg_view("input/file",array('internalname' => 'icon'));

        ?>
        </label>
    </p>
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
