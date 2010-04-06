<?php
    set_context('search');

    $query = get_input('q');
                
    if ($query)
    {
        $title = sprintf(elgg_echo('search:title_with_query'),$query);
    }    
    else
    {
        $title = elgg_echo('search:title');
    }
    $content = elgg_view('org/search', array('query' => $query, 'sector' => get_input('sector')));

    $heading = elgg_view('page/simpleheading', array('title' => $title));
    $body = elgg_view_layout('one_column_padded',$heading, $content);

    page_draw($title,$body);
?>