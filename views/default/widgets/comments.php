<?php
	$widget = $vars['widget'];
?>
<div style='clear:both'></div>
<div id='comments'>

<?php
    $comments = $widget->query_comments()->show_disabled(true)->filter();
    
    if (sizeof($comments) > 0)
    {
        echo "<h4>".__('comment:title')." ({$widget->num_comments})</h4>";
    }
    
    foreach ($comments as $comment)
    {
        echo view('widgets/comment_view', array('comment' => $comment));
    }
?>
<form id='comment_form' method='POST' action='<?php echo $widget->get_url() ?>/add_comment'>
<?php echo view('input/securitytoken'); ?>

<h4><?php echo __('comment:add'); ?></h4>

<div class='input'>
<?php 
    echo view('input/longtext', array('name' => 'content')); 
    echo view('widgets/comment_user_info');
    echo view('input/submit', array('value' => __('comment:publish'))); 
?>

</div>

</form>
</div>
