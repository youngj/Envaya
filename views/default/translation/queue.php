<?php

    $lang = $vars['lang'];

    $offset = (int)get_input('offset');

    $limit = 5;

    $entities = Translation::filterByLanguageAndOwner($lang, 0, $limit, $offset);
    $count = Translation::filterByLanguageAndOwner($lang, 0, $limit, $offset, true);

    echo elgg_view_entity_list($entities, $count, $offset, $limit, false, false, $pagination = true);

?>