<?php
    $member = $vars['entity'];
?>

<form action='action/org/saveTeamMember' enctype='multipart/form-data' method='POST'>

<?php echo elgg_view('input/securitytoken'); ?>

<div class='input'>
<label><?php echo elgg_echo('widget:team:name'); ?></label>
<?php
    echo elgg_view('input/text', 
        array(
            'value' => $member->name,
            'internalname' => 'name', 
            'trackDirty' => true,
        )
    );
?>
</div>

<div class='input'>
<label><?php echo elgg_echo('widget:team:description'); ?></label>
<?php
    echo elgg_view('input/longtext', 
        array(
            'value' => $member->description,
            'internalname' => 'description', 
            'trackDirty' => true,
            'js' => "style='height:60px'",            
        )
    );
?>    
</div>

<div class='input'>
<label><?php echo elgg_echo('widget:team:photo'); ?></label><br />
<?php echo elgg_view('input/image', array(
        'internalname' => 'image',
        'sizes' => TeamMember::getImageSizes(),    
        'current' => ($member && $member->hasImage() ? $member->getImageUrl('small') : null),
        'deletename' => 'deleteimage',
)) ?>    
</div>

<?php
    echo elgg_view('input/hidden', 
        array('internalname' => 'member_guid', 
            'value' => $member->guid)); 

    echo elgg_view('input/submit', 
        array('internalname' => 'submit', 
            'class' => "submit_button addUpdateButton",
            'trackDirty' => true,
            'value' => elgg_echo('savechanges'))); 

?>

</form>
