<div class='section_content padded'>
<?php
    $topic = $vars['topic'];
       
    $limit = 20;
    $offset = (int)get_input('offset');
    
    $query  = $topic->query_messages()->limit($limit, $offset);
    
    $count = $topic->num_messages;
    $messages = $query->filter();

    $elements = array();
    
    foreach ($messages as $message)
    {
        $elements[] = view('discussions/message_edit_item', array(
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
    echo "<strong><a href='{$topic->get_url()}/add_message'>".__('discussions:add_message')."</a></strong>";
?>
</div>