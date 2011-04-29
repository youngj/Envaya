<?php
	$widget = $vars['widget'];
    echo "<div class='comment_link'>";        
    echo "<a href='{$widget->get_url()}#comments'>".__('comment:title')." ({$widget->num_comments})</a>";
    echo "</div>";    
?>
