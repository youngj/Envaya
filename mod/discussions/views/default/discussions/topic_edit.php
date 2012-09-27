<?php
    $topic = $vars['topic'];
    
    ob_start();
    
    echo "<table class='inputTable' style='margin:0 auto'>";
    echo "<tr><th>".__('discussions:subject')."</th>";
    echo "<td>";
    echo view('input/text', array(
        'name' => 'subject', 
        'track_dirty' => true, 
        'value' => $topic->subject, 
        'style' => "width:350px"
    ));
    echo "</td>";
    echo "</tr>";
    echo "<tr><th>&nbsp;</th><td>";
    echo view('input/submit', array('value' => __('savechanges')));
    echo "</td></tr>";
    echo "</table>";
    
    $content = ob_get_clean();
         
    echo view('section', array(
        'header' => __('settings'), 
        'content' => view('input/form', array('action' => $topic->get_edit_url(), 'body' => $content))
    ));
    
    ob_start();
       
    $limit = 20;
    $offset = Input::get_int('offset');
    
    $query  = $topic->query_messages()->limit($limit, $offset);
    
    $count = $topic->num_messages;
    $messages = $query->filter();

    $items = array();
    
    foreach ($messages as $message)
    {
        $items[] = view('discussions/message_edit_item', array(
            'message' => $message,
            'topic' => $topic
        ));
    }    

    echo view('paged_list', array(
        'offset' => $offset,
        'limit' => $limit,
        'count' => $count,
        'items' => $items,
        'separator' => "<div style='margin:10px 0px' class='separator'></div>"
    ));
    
    echo "<br />";
    
    $user = $topic->get_container_entity();    
    $widget = Widget_Discussions::get_for_entity($user);    
    if ($widget)
    {    
        echo "<div style='float:right'>";    
        echo "<a href='{$widget->get_url()}/edit'>".__('discussions:back_to_topics'). "</a>";
        echo "</div>";
    }
        
    echo "<strong><a href='{$topic->get_url()}/add_message?offset={$offset}#add_message'>".__('discussions:add_message')."</a></strong>";
    
    $content = ob_get_clean();
    
    echo view('section', array('header' => __('discussions:messages'), 'content' => $content));