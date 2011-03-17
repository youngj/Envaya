<?php 
    $widget = $vars['widget'];
    $org = $widget->get_container_entity();
    
    $feedNames = array();
    foreach ($org->query_relationships()
                ->where('subject_guid > 0')
                ->where('approval & ? > 0', OrgRelationship::SelfApproved)
                ->filter() as $relationship)
    {
        $feedNames[] = get_feed_name(array('user' => $relationship->subject_guid));
    }
    
    //$feedName = get_feed_name(array('network' => $org->guid));
    $maxItems = 20;    
    $items = FeedItem::query_by_feed_names($feedNames)->limit($maxItems)->filter();    
    
    if ($items)
    {
?>
<div class='section_header'><?php echo __('widget:news:latest'); ?></div>
<div class='section_content'>
<div id='feed_container'>
<?php	
	echo view('feed/list', array('items' => $items));
?>
</div>  
</div>
<?php 
    }
?>