<?php
    $topic = $vars['topic'];
    $org = $topic->get_root_container_entity();

    if ($org->is_approved() && $topic->can_edit() && !$topic->get_metadata('invited_emails'))
    {            
        echo " <a style='font-weight:bold;white-space:nowrap' href='{$topic->get_url()}/invite'>" 
            . __('discussions:invite_link') . "</a>";
    }