<?php 
    $widget = $vars['widget'];
    $org = $widget->get_container_entity();
    
    $feedNames = array();
    foreach (Relationship::query_for_user($org)
                ->where('subject_guid > 0')
                ->filter() as $relationship)
    {
        $feedNames[] = FeedItem::make_feed_name(array('user' => $relationship->subject_guid));
    }
    
    //$feedName = FeedItem::make_feed_name(array('network' => $org->guid));
    $maxItems = 10;    
    $items = FeedItem::query_by_feed_names($feedNames)
        ->where_visible_to_user()
        ->limit($maxItems)
        ->filter();    
    
    if ($items)
    {
?>
<div class='section_header'><?php echo __('widget:updates'); ?></div>
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