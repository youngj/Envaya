<?php
    $message = $vars['entity'];
    
    echo "<div id='msg{$message->guid}'>";
    echo "<strong>{$message->get_from_link()}</strong>";    
    echo "<div class='blog_date'>". $message->get_date_text().    "</div>";    
    echo $message->render_content();
    echo "</div>";
?>
