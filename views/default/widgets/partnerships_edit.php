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
            $partnerOrgEntity = $p->getPartner();
            $pDescription = $p->description;
            
            echo "<img style='float:left;' src='" . $partnerOrgEntity->getIcon('tiny') . "' />";
            echo "<a href='{$partnerOrgEntity->getUrl()}'>{$partnerOrgEntity->name}</a>";
            echo "<div style='clear:both;' />";
            
            echo elgg_echo('widget:partnerships:description');
            echo "<div class='input'>";
            
            echo elgg_view('input/longtext', array(
                'internalname' => "partnershipDesc{$p->guid}",
                'js' => 'style="width:350px;height:150px;"',
                'value' => $pDescription
            ));
            
            echo "</div>";
            echo "<br><br>";
        }
    }
    $content = ob_get_clean();

       echo elgg_view("widgets/edit_form", array(
           'widget' => $widget,
           'body' => $content
       ));

?>
</div>