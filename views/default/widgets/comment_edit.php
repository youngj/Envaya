<?php
    $comment = $vars['comment'];
?>
<div class='section_content padded'>
<h3 style='padding-bottom:8px'><?php echo __('comment:edit'); ?></h3>
<form method='POST' action='<?php echo $comment->get_base_url(); ?>/edit'>
<?php 
    echo view('input/tinymce', array(
        'name' => 'content', 
        'track_dirty' => true, 
        'value' => $comment->content
    ));
    echo view('widgets/comment_user_info', array(
        'name' => $comment->name,
        'location' => $comment->location
    ));
    echo view('input/securitytoken');
    
    echo "<div style='float:right'>";   
    echo "<br />";
    echo "<a href='{$comment->get_url()}'>".__('canceledit'). "</a>";
    echo "</div>";    
    
    echo view('input/submit', array('value' => __('savechanges')));        
?>
</div>