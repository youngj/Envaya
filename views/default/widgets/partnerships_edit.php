<div class='padded'>
<?php
    $widget = $vars['widget'];
    $org = $widget->getContainerEntity();
    
    $offset = (int) get_input('offset');
    $limit = 10;

    $count = $org->getPartnerships($limit, $offset, true);
    $entities = $org->getPartnerships($limit, $offset);
    
    ob_start();
    
    if(!$count)
    {
        echo elgg_echo("partner:none");
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