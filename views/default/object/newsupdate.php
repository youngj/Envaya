<?php

    $entity = $vars['entity'];
    $url = $entity->get_url();
    $single_post = @$vars['single_post'];

    if (!$single_post)
    {
        echo "<div class='blog_post_wrapper padded'>";
    }
?>

<div class="blog_post">

    <?php
    
        echo $entity->render_content();

        echo "<div class='blog_date'>";
        if (!$single_post)
        {
            echo "<a href='{$entity->get_url()}'>{$entity->get_date_text()}</a>";
        }
        else
        {
            echo $entity->get_date_text();
        }
        echo "</div>";
		
		if (!$single_post)
		{
			echo "<div class='comment_link'>";
			echo "<a href='{$entity->get_url()}#comments'>".sprintf(__('comment:count'), $entity->num_comments)."</a>";
			echo "</div>";
		}
    ?>
    <div style='clear:both'></div>
</div>

<?php

    if (!$single_post)
    {
        echo "</div>";
    }
	
	if ($single_post)
	{
		echo view('org/comments', array('entity' => $entity));
	}