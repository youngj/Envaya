<div class='padded section_content'>
<?php
    $widget = $vars['widget'];

    if (!$widget->content)
    {
        echo sprintf(__('widget:empty'), escape($widget->get_title()));
    }
    else
    {
        echo $widget->render_content();
    }

?>
<div style='clear:both'></div>

<?php
/*
	if (get_input('comments'))
	{
		echo view('org/comments', array('entity' => $widget));
	}
	else
	{
		echo "<div class='comment_link'>";
		echo "<a href='{$widget->get_url()}?comments=1#comments'>".sprintf(__('comment:count'), $widget->num_comments)."</a>";
		echo "</div>";	
	}
	*/
?>

</div>