<?php
    $topic = $vars['topic'];
    $org = $topic->get_container_entity();
       
    $limit = 20;
    $offset = (int)get_input('offset');
    
    $query  = $topic->query_messages()->show_disabled(true)->limit($limit, $offset);
    
    $count = $topic->num_messages;
    $messages = $query->filter();    
    
    echo "<div class='section_content padded'>";
    echo "<h3 style='padding-bottom:8px'>".escape($topic->translate_field('subject'))."</h3>";
    
    $elements = array();
    
    foreach ($messages as $message)
    {
        $elements[] = view('discussions/topic_view_message', array(
            'message' => $message,
            'topic' => $topic
        ));    
    }
    
    echo view('paged_list', array(
        'offset' => $offset,
        'limit' => $limit,
        'count' => $count,
        'elements' => $elements,
        'separator' => "<div style='margin:10px 0px' class='separator'></div>"
    ));
    
    echo "<br />";

        
    $widget = $org->get_widget_by_class('Discussions');    
    
    echo "<div style='float:right'>";    
    echo "<a href='{$widget->get_url()}'>".__('discussions:back_to_topics'). "</a>";
    echo "</div>";
    
    if (!@$vars['show_add_message'])
    {
        echo "<h3><a id='add_message' href='{$topic->get_url()}/add_message?offset={$offset}#add_message'>";
        echo __('discussions:add_message');
        echo "</a></h3>";
    }
    else
    {
        echo "<h3><div id='add_message' style='padding-bottom:8px;'>";
        echo __('discussions:add_message');
        echo " <a href='{$topic->get_url()}?offset={$offset}#add_message'>(".__('hide').")</a>";
        echo "</div></h3>";    
        echo view('discussions/add_message_form', $vars);    
    }        
    echo "</div>";
?>
