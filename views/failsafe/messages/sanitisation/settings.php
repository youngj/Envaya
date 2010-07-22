<?php

	/**
	 * Elgg settings not found message
	 * Is saved to the errors register when settings.php cannot be found
	 * 
	 * @package Elgg
	 * @subpackage Core
	 * @author Curverider Ltd
	 * @link http://elgg.org/
	 */

	if ($vars['settings.php'])
	{
		echo __('installation:settings:dbwizard:savefail');
?>
<div>
	<textarea rows="50" cols="120"><?php echo $vars['settings.php']; ?></textarea>
</div>
<?php
	}
	else
	{
		
		echo autop(__('installation:error:settings'));
?>
<div>
	<h2><?php echo __('installation:settings:dbwizard:prompt'); ?></h2>
	<form method="POST">
		<table cellpadding="0" cellspacing="10" style="background:#f1f1f1;">
			<tr><td valign="top"><?php echo __('installation:settings:dbwizard:label:user'); ?></td><td valign="top"> <input type="text" name="db_install_vars[CONFIG_DBUSER]" /></td></tr>
			<tr><td valign="top"><?php echo __('installation:settings:dbwizard:label:pass'); ?></td><td valign="top"> <input type="password" name="db_install_vars[CONFIG_DBPASS]" /></td></tr>
			<tr><td valign="top"><?php echo __('installation:settings:dbwizard:label:dbname'); ?></td><td valign="top"> <input type="text" name="db_install_vars[CONFIG_DBNAME]" /></td></tr>
			<tr><td valign="top"><?php echo __('installation:settings:dbwizard:label:host'); ?></td><td valign="top"> <input type="text" name="db_install_vars[CONFIG_DBHOST]" value="localhost" /></td></tr>
			<tr><td valign="top"><?php echo __('installation:settings:dbwizard:label:prefix'); ?></td><td valign="top"> <input type="text" name="db_install_vars[CONFIG_DBPREFIX]" value="elgg" /></td></tr>
		</table>
		
		<input type="submit" name="<?php echo __('save'); ?>" value="<?php echo __('save'); ?>" />
	</form>
</div>
<?php } ?>