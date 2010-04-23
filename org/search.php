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

    $body = elgg_view_layout('one_column_padded', elgg_view_title(elgg_echo('search:title')), $content);

    page_draw($title,$body);
?>