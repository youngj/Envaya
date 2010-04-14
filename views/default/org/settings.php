<?php
    global $CONFIG;
    $user = page_owner_entity();
    
    if ($user && $user instanceof Organization) 
    {
?>

<div class='section_header'>    
    <?php echo elgg_echo('theme') ?>
</div>
      
<?php echo elgg_view('org/editTheme'); ?>

<?php echo elgg_view('input/submit', array('value' => elgg_echo('savechanges'), 'trackDirty' => true)); ?>
&nbsp;
    </div>

<?php echo elgg_view('org/editIcon'); ?>
<?php echo elgg_view('input/submit', array('value' => elgg_echo('savechanges'), 'trackDirty' => true)); ?></div>   

<?php } ?>