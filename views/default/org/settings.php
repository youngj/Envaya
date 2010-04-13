<?php
    global $CONFIG;
    $user = page_owner_entity();
    
    if ($user && $user instanceof Organization) 
    {
?>

    
<?php echo elgg_view('org/editTheme'); ?>

<?php echo elgg_view('input/submit', array('value' => elgg_echo('savechanges'), 'trackDirty' => true)); ?>
&nbsp;
    </div>

<?php echo elgg_view('org/editIcon'); ?>
<?php echo elgg_view('input/submit', array('value' => elgg_echo('savechanges'), 'trackDirty' => true)); ?></div>   

<?php } ?>