<?php
    $topic = $vars['topic'];
       
    $limit = 20;
    $offset = (int)get_input('offset');
    
    $query  = $topic->query_messages()->limit($limit, $offset);
    
    $count = $topic->num_messages;
    $messages = $query->filter();    

    ob_start();
    
    echo view('paged_list', array(
        'offset' => $offset,
        'limit' => $limit,
        'count' => $count,
        'entities' => $messages,
        'separator' => "<div style='margin:10px 0px' class='separator'></div>"
    ));
    
    echo "<br />";
    echo "<strong><a href='{$topic->get_url()}/add_message'>".__('discussions:add_message')."</a></strong>";
    
    $content = ob_get_clean();
    
    echo view('section', array('header' => escape($topic->subject), 'content' => $content));
?>
