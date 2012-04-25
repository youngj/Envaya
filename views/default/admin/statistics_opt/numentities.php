
<div class="admin_statistics">
    <h3><?php echo __('admin:statistics:label:numentities'); ?></h3>
    <table class='inputTable'>
    <?php
    
        $classes = array_values(PrefixRegistry::all_classes());
        sort($classes);
    
        foreach ($classes as $class)
        {
            echo "<tr><th>".escape($class)."</th><td>".$class::query()->count()."</td></tr>";
        }
    ?>
    </table>
</div>