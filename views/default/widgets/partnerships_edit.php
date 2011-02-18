<?php
    $widget = $vars['widget'];
    $org = $widget->get_container_entity();

?>

<div class='section_header'><?php echo __('partner:find_new'); ?></div>
<div class='section_content padded'>
<?php if ($org->is_approved()) { ?>
<p>
<?php echo sprintf(__('partner:instructions'), "<strong>".__('partner:request')."</strong>"); ?>
</p>

<p>
<strong><a href='org/search'><?php echo __('partner:search') ?></a></strong> &middot;
<strong><a href='org/browse?zoom=10&lat=<?php echo escape($org->latitude) ?>&long=<?php echo escape($org->longitude) ?>'><?php echo __('partner:browse') ?></a></strong>
</p>
<?php } else { ?>
<p>
<?php echo __('partner:needapproval') ?>
</p>
<p>
<?php echo __('partner:wait') ?>
</p>
<?php } ?>
</div>

<div class='section_header'><?php echo __('partner:current'); ?></div>
<div class='section_content padded'>
<?php
    $offset = (int) get_input('offset');
    $limit = 10;

    $query = $org->query_partnerships()->limit($limit, $offset);
    
    $count = $query->count();
    $entities = $query->filter();
    
    ob_start();
    
    if(!$count)
    {
        echo "<div>".__("partner:none")."</div>";
    }
    else
    {
        echo view('navigation/pagination',array(
            'baseurl' => $widget->get_edit_url(),
            'offset' => $offset,
            'count' => $count,
            'limit' => $limit
        ));
        
        foreach($entities as $p)
        {
            $partner = $p->get_partner();
            ?>            
            
<div class="partnership_view">    
    <a class='feed_org_icon' href='<?php echo $partner->get_url() ?>'><img src='<?php echo $partner->get_icon('small') ?>' /></a>
    
    <div class='feed_content'>
        <a class='feed_org_name' href='<?php echo $partner->get_url() ?>'><?php echo escape($partner->name); ?></a><br />
        <label><?php echo __('widget:partnerships:description'); ?></label><br />

        <?php echo view('input/longtext', array(
                'name' => "partnershipDesc{$p->guid}",
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

       echo view("widgets/edit_form", array(
           'widget' => $widget,
           'body' => $content
       ));

?>
</div>