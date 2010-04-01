<?php
    $org = $vars['entity'];
    $partner = $vars['partner'];
    
?>
<div class='section_content padded'>
<form action='action/org/createPartner' method='POST'>
<?php echo elgg_view('input/securitytoken') ?>
<div class="partnership_view">    
    <a class='feed_org_icon' href='<?php echo $partner->getURL() ?>'><img src='<?php echo $partner->getIcon('small') ?>' /></a>
    
    <div class='feed_content'>
        <a class='feed_org_name' href='<?php echo $partner->getUrl() ?>'><?php echo escape($partner->name); ?></a><br />
    </div>
    <div style='clear:both;'></div>        
    
</div>
<label><?php echo elgg_echo('partner:confirm:instructions') ?></label>
<div>
<?php 
echo elgg_view('input/hidden', array('internalname' => 'partner_guid', 'value' => $partner->guid));

echo elgg_view('input/submit',array(
    'value' => elgg_echo('partner:confirm:button')
));

?>
</div>


</form>
</div>
