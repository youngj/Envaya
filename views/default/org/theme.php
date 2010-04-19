<?php
    $user = $vars['entity'];
?>

<form action='action/org/theme' method='POST'>

<?php echo elgg_view('input/securitytoken'); ?>

<?php echo elgg_view('org/editIcon'); ?></div>

<?php
echo elgg_view('input/submit',array(
    'value' => elgg_echo('savechanges'),
    'trackDirty' => true,
));

?>

<?php echo elgg_view('org/editTheme'); ?>

<?php 
echo elgg_view('input/hidden', array('internalname' => 'guid', 'value' => $user->guid));

echo elgg_view('input/submit',array(
    'value' => elgg_echo('savechanges'),
    'trackDirty' => true,
));
?>
</div>
</form>
