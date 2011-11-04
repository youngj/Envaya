<?php    
    $query = $vars['query'];
    $language = $vars['language'];
    $base_url = $vars['base_url'];
    $filter = $vars['filter'];
    
    echo view('translate/filter_form', array(
        'action' => "/tr/{$language->code}/interface",
        'filter' => $filter,
    ));               
    
    echo view('translate/key_table', array(
        'query' => $query,
        'base_url' => $base_url,
        'language' => $language,
    ));
