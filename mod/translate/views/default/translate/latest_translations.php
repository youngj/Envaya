<?php
    $language = $vars['language'];
    $query = $language->query_translations()->order_by('time_created desc');

    echo view('translate/translations', array('query' => $query, 'language' => $language));