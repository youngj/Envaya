<?php    
    $query = $vars['query'];
    $language = $vars['language'];
    $base_url = $vars['base_url'];
    
    echo view('translate/key_table', array(
        'query' => $query,
        'base_url' => $base_url,
        'language' => $language,
    ));
