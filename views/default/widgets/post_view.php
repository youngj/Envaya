<?php
    $widget = $vars['widget'];
    $is_primary = $vars['is_primary'];      
?>
<div class='section_content padded'>
<?php
    echo view($widget->get_title_view(), $vars);
    echo view($widget->get_content_view(), $vars);        
    echo view($widget->get_date_view(), $vars);
       
    if (!$is_primary)
    {
        echo view('widgets/comment_link', $vars);
    }
    else
    {	
        echo view('widgets/post_nav', $vars);        
		echo view('widgets/comments', $vars);
	}
?>
</div>