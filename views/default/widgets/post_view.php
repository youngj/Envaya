<?php
    $widget = $vars['widget'];
    $is_primary = @$vars['is_primary'];        
?>
<div class='section_content padded'>
<?php
    echo view('widgets/post_view_content', $vars);        
    if ($is_primary)
    {
		echo view('widgets/comments', $vars);
	}
?>
</div>