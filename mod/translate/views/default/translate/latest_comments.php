<?php
    $language = $vars['language'];
    $query = $language->query_comments()->order_by('time_created desc, guid desc');

    echo view('translate/comments', array('query' => $query, 'language' => $language));