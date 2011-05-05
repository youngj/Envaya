<?php
    $topic = $vars['topic'];
    $org = $topic->get_root_container_entity();

    echo " <a style='font-weight:bold;white-space:nowrap' href='javascript:emailShare(".json_encode($org->username).");'>" 
        . __('discussions:invite_link') . "</a>";