<?php
    $topic = $vars['topic'];
    $org = $topic->get_container_entity();
       
    $limit = 20;
    $offset = (int)get_input('offset');
    
    $query  = $topic->query_messages()->show_disabled(true)->limit($limit, $offset);
    
    $count = $topic->num_messages;
    $messages = $query->filter();    

    
    echo "<div class='section_content padded'>";
    echo "<h3 style='padding-bottom:8px'>".escape($topic->subject)."</h3>";
    
    $elements = array();
    
    foreach ($messages as $message)
    {
        $elements[] = view('discussions/topic_view_message', array('message' => $message));    
    }
    
    echo view('paged_list', array(
        'offset' => $offset,
        'limit' => $limit,
        'count' => $count,
        'elements' => $elements,
        'separator' => "<div style='margin:10px 0px' class='separator'></div>"
    ));
    
    echo "<br />";

    $widget = $org->get_widget_by_class('WidgetHandler_Discussions');    
    
    echo "<div style='float:right'>";    
    echo "<a href='{$widget->get_url()}'>".__('discussions:back_to_topics'). "</a>";
    echo "</div>";
    
    echo "<strong><a href='{$topic->get_url()}/add_message'>".__('discussions:add_message')."</a></strong>";
    echo "</div>";
?>
