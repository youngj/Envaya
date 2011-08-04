<?php
    $language = $vars['language'];
    $query = $language->query_translations()
        ->where('owner_guid <> 0')
        ->order_by('time_created desc');

    echo view('translate/translations', array(
        'query' => $query, 
        'language' => $language
    ));