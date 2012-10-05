
<div class="admin_statistics">
    <h3><?php echo __('admin:statistics:label:basic'); ?></h3>
    <table>
        <tr class="even">
            <td class="column_one"><b><?php echo __('admin:statistics:label:numusers'); ?> :</b></td>
            <td><?php echo User::query()->count(); ?> <?php echo __('total'); ?> </td>
        </tr>

    </table> 
</div>  