<?php

    $lang = $vars['lang'];

    $offset = (int)get_input('offset');

    $limit = 5;

    $query = Translation::query_by_language_and_owner($lang, 0);
    $query->limit($limit, $offset);
    $entities = $query->filter();
    $count = $query->count();

    echo view_entity_list($entities, $count, $offset, $limit);

?>