<?php
	/**
	 * Elgg statistics screen
	 * 
	 * @package Elgg
	 * @subpackage Core
	 * @author Curverider Ltd
	 * @link http://elgg.org/
	 */

	// Work out number of users
	$users_stats = get_number_users();
	$total_users = get_number_users(true);
	
	
	global $CONFIG;
		
?>
<div class="admin_statistics">
    <h3><?php echo __('admin:statistics:label:basic'); ?></h3>
    <table>
        <tr class="even">
            <td class="column_one"><b><?php echo __('admin:statistics:label:numusers'); ?> :</b></td>
            <td><?php echo $users_stats; ?> <?php echo __('active'); ?> / <?php echo $total_users; ?> <?php echo __('total') ?></td>
        </tr>

    </table> 
</div>  