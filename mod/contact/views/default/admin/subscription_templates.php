<div class='padded'>
<?php   
    $subscription = $vars['subscription'];
    
    echo $vars['header'];

    $query = $vars['query'];
    $limit = 10;
    $offset = Input::get_int('offset');
    
    $templates = $query->limit($limit, $offset)->filter();
    $count = $query->count();
    
    $items = array();
    
    $escFrom = urlencode(Input::get_string('from'));
    
    foreach ($templates as $template)
    {
        ob_start();
        
        echo "<div style='padding:3px'>";
        
        $can_send = $template->can_send_to($subscription);
        
        $escSubject = escape($template->get_description());
        
        if ($can_send)
        {
            echo "<a href='{$template->get_url()}/send?subscriptions[]={$subscription->guid}&from=$escFrom'><strong>$escSubject</strong></a>";            
            echo " (".get_date_text($template->time_created).")";            
        }
        else
        {
            echo "<span style='color:#999'>$escSubject</span>";
            
            $outgoing_message = $template->query_outgoing_messages_for_subscription($subscription)->get();
            if ($outgoing_message)
            {            
                echo " ({$outgoing_message->get_status_text()} ".get_date_text($outgoing_message->time_created).") ";
                
                echo view('input/post_link', array(
                    'href' => "{$template->get_url()}/reset_outgoing?id={$outgoing_message->id}",
                    'text' => 'reset'
                ));
            }
        }
                
        echo "</div>";
        
        $items[] = ob_get_clean();
    }
   
    echo view('paged_list', array(
        'items' => $items,
        'count' => $count,
        'offset' => $offset,
        'limit' => $limit,
        'separator' => "<div class='separator'></div>"
    ));
?>
</div>