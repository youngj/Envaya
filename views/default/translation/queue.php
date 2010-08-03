<?php

    $lang = $vars['lang'];

    $offset = (int)get_input('offset');

    $limit = 5;

    $query = Translation::queryByLanguageAndOwner($lang, 0);
    $entities = $query->filter();
    $count = $query->count();

    echo view_entity_list($entities, $count, $offset, $limit);

?>