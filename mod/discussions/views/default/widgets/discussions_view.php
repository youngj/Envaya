<?php
    $widget = $vars['widget'];
    $org = $widget->get_container_entity();

    $limit = 20;
    $offset = Input::get_int('offset');
    
    $query = DiscussionTopic::query_for_user($org)->limit($limit, $offset);
        
    $count = $query->count();
        
    ob_start();        
        
    if ($count > 0)
    {          
        $topics = $query->filter();
    
        echo view('paged_list', array(
            'offset' => $offset,
            'limit' => $limit,
            'count' => $count,
            'items' => array_map(function($topic) { 
                return view('widgets/discussions_view_topic_item', array('topic' => $topic));
            }, $topics),
        ));
    }        
    else
    {
        echo __('discussions:no_topics');
    }
    
    echo "<br />";
    echo "<div style='padding:0px 5px;font-weight:bold'>";
    echo "<div style='float:right;font-weight:normal'>";
    echo "<a href='/pg/discussions'>".__('discussions:other_link')."</a>";
    echo "</div>";
    echo "<a href='{$org->get_url()}/topic/new'>".__('discussions:add_topic')."</a>";
    echo "</div>";
    
    $content = ob_get_clean();
    
    echo "<div class='section_content padded'>$content</div>";