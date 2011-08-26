<?php
    $language = $vars['language'];
    $query = $language->query_translations()
        ->where('source = ?', Translation::Human)
        ->order_by('time_created desc, guid desc');

    echo view('translate/translations', array(
        'query' => $query, 
        'language' => $language
    ));