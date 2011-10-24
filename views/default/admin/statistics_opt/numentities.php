
<div class="admin_statistics">
    <h3><?php echo __('admin:statistics:label:numentities'); ?></h3>
    <table class='inputTable'>
    <?php
        $types = Database::get_rows("SELECT subtype_id, count(*) as count from entities group by subtype_id");
        foreach ($types as $type)
        {
            echo "<tr><th>".escape($type->subtype_id)."</th><td>{$type->count}</td></tr>";
        }
    ?>
    </table>
</div>