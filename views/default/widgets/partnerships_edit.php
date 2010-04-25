<?php
    $widget = $vars['widget'];
    $org = $widget->getContainerEntity();

?>

<div class='section_header'><?php echo elgg_echo('partner:find_new'); ?></div>
<div class='section_content padded'>
<?php if ($org->isApproved()) { ?>
<p>
<?php echo sprintf(elgg_echo('partner:instructions'), "<strong>".elgg_echo('partner:request')."</strong>"); ?>
</p>

<p>
<strong><a href='org/search'><?php echo elgg_echo('partner:search') ?></a></strong> &middot;
<strong><a href='org/browse?zoom=10&lat=<?php echo escape($org->latitude) ?>&long=<?php echo escape($org->longitude) ?>'><?php echo elgg_echo('partner:browse') ?></a></strong>
</p>
<?php } else { ?>
<p>
<?php echo elgg_echo('partner:needapproval') ?>
</p>
<p>
<?php echo elgg_echo('partner:wait') ?>
</p>
<?php } ?>
</div>

<div class='section_header'><?php echo elgg_echo('partner:current'); ?></div>
<div class='section_content padded'>
<?php
    $offset = (int) get_input('offset');
    $limit = 10;

    $count = $org->getPartnerships($limit, $offset, true);
    $entities = $org->getPartnerships($limit, $offset);
    
    ob_start();
    
    if(!$count)
    {
        echo "<div>".elgg_echo("partner:none")."</div>";
    }
    else
    {
        echo elgg_view('navigation/pagination',array(
            'baseurl' => $widget->getEditURL(),
            'offset' => $offset,
            'count' => $count,
            'limit' => $limit
        ));
        
        foreach($entities as $p)
        {
            $partner = $p->getPartner();
            ?>            
            
<div class="partnership_view">    
    <a class='feed_org_icon' href='<?php echo $partner->getURL() ?>'><img src='<?php echo $partner->getIcon('small') ?>' /></a>
    
    <div class='feed_content'>
        <a class='feed_org_name' href='<?php echo $partner->getUrl() ?>'><?php echo escape($partner->name); ?></a><br />
        <label><?php echo elgg_echo('widget:partnerships:description'); ?></label>

        <?php echo elgg_view('input/longtext', array(
                'internalname' => "partnershipDesc{$p->guid}",
                'js' => 'style="width:350px;height:150px;"',
                'trackDirty' => true,
                'value' => $p->description
            )); ?>
    
    </div>
    <div style='clear:both;'></div>        
</div>
           
            <?php
        }
    }
    $content = ob_get_clean();

       echo elgg_view("widgets/edit_form", array(
           'widget' => $widget,
           'body' => $content
       ));

?>
</div>