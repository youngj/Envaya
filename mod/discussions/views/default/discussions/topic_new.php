<?php
    $user = $vars['user'];
  
    ob_start();
    
?>
<form method='POST' action='<?php echo $user->get_url(); ?>/topic/new'>
<div class='input'>
<label><?php echo __('discussions:subject'); ?></label><br />
<?php 
    echo view('input/text', array('name' => 'subject')); 
?>
</div>
<?php 
    echo view('input/tinymce', array('name' => 'content'));    
    echo view('widgets/comment_user_info');
    
    $widget = Widget_Discussions::get_for_entity($user);    
    
    if ($widget)
    {
        echo "<div style='float:right'><br /><br />";    
        echo "<a href='{$widget->get_url()}'>".__('discussions:back_to_topics'). "</a>";
        echo "</div>";
    }

    echo view('input/uniqid');    
    echo view('input/securitytoken');
    echo view('input/submit', array('value' => __('publish')));
 ?>
</form>
<?php
    echo view('focus', array('name' => 'subject'));

    $content = ob_get_clean();
    
    echo view('section', array(
        'header' => __('discussions:add_topic'), 
        'content' => $content
    ));
