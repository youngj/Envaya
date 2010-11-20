<?php
	$entity = $vars['entity'];
?>

<div id='comments'>

<?php
    $comments = $entity->query_comments()->show_disabled(true)->filter();
    
    if (sizeof($comments) > 0)
    {
        echo "<h4>".sprintf(__('comment:count'), $entity->num_comments)."</h4>";
    }
    
    foreach ($comments as $comment)
    {
        echo view_entity($comment);
    }
?>
<form id='comment_form' method='POST' action='<?php echo $entity->get_url() ?>/post_comment'>
<?php echo view('input/securitytoken'); ?>

<h4><?php echo __('comment:add'); ?></h4>

<div class='input'>
<?php echo view('input/longtext', array('internalname' => 'content')); ?>

<div>
<label><?php echo __('comment:name'); ?></label> 
<?php 

$user = Session::get_loggedin_user();
if ($user)
{
    echo escape($user->name);
}
else
{
    echo view('input/text', array('internalname' => 'name', 'class' => 'comment_name_input input-text')); 
}
?>
</div>

</div>

<?php echo view('input/submit', array('value' => __('comment:publish'))); ?>

</div>

</form>
</div>
