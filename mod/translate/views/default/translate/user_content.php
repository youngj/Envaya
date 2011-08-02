<?php
    $filter = $vars['filter'];
    $language = $vars['language'];
    $base_url = $vars['base_url'];
    $query = $vars['query'];

    echo view('translate/filter_form', array(
        'action' => "/tr/{$language->code}/content",
        'filter' => $filter,
        'hide_query' => true,
    ));    
        
    echo view('translate/key_table', array(
        'query' => $query,
        'language' => $language,
        'base_url' => $base_url
    ));
