<?php
	/**
	 * Elgg statistics screen
	 * 
	 * @package Elgg
	 * @subpackage Core

	 * @author Curverider Ltd

	 * @link http://elgg.org/
	 */

	// users online

	$users_online = Statistics::get_online_users();
?>

<div class="admin_users_online">
    <h3><?php echo __('admin:statistics:label:onlineusers'); ?></h3>
    <?php echo $users_online; ?>
</div>
