<?php	
	$entity_stats = Statistics::get_entity_stats();
	$even_odd = "";
?>
<div class="admin_statistics">
    <h3><?php echo __('admin:statistics:label:numentities'); ?></h3>
    <table class='inputTable'>
        <?php
            foreach ($entity_stats as $subtype_id => $count)
            {
                echo "<tr><th>".escape($subtype_id)."</th><td>{$count}</td></tr>";
            }
        ?>
    </table>
</div>