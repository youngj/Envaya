<?php
    $message = $vars['message'];
    
    echo $message->render_content();
    echo "<br /><br />";
    echo "<a href='{$message->get_url()}'>{$message->get_url()}</a>";
    echo "<br />";