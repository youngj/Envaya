<?php
    $widget = $vars['widget'];
    $is_primary = @$vars['is_primary'];        
?>
<div class='section_content padded'>
<?php
    echo $widget->render_content();
    echo "<div class='blog_date'>";        
    $date_text = $widget->get_date_text();    
    if (!$is_primary)
    {
        echo "<a href='{$widget->get_url()}'>{$date_text}</a>";
    }
    else
    {
        echo $date_text;
    }
    echo "</div>";    
    if ($is_primary)
    {
        echo view('widgets/post_nav', array('widget' => $widget));    
    }    
?>
<div style='clear:both'></div>
<?php
	if ($is_primary)
	{
		echo view('widgets/comments', array('widget' => $widget));
	}
    else
    {
        echo "<div class='comment_link'>";
        echo "<a href='{$widget->get_url()}#comments'>".sprintf(__('comment:count'), $widget->num_comments)."</a>";
        echo "</div>";
    }
?>
</div>