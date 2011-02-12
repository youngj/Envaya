<?php
	$users_stats = Statistics::get_number_users();
	$total_users = Statistics::get_number_users(true);		
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