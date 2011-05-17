<?php
    $topic = $vars['topic'];
    $org = $topic->get_root_container_entity();
    
    echo "<script type='text/javascript'>".view('js/share')."</script>";
    echo " <a style='font-weight:bold;white-space:nowrap' href='javascript:emailShare(".json_encode($org->username).");'>" 
        . __('discussions:invite_link') . "</a>";