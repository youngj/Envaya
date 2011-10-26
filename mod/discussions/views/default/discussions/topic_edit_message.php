<?php
    $message = $vars['message'];
?>
<div class='section_content padded'>
<h3 style='padding-bottom:8px'><?php echo __('discussions:edit_message'); ?></h3>
<form method='POST' action='<?php echo $message->get_base_url(); ?>/edit'>
<?php 
    echo view('input/tinymce', array(
        'name' => 'content', 
        'track_dirty' => true, 
        'value' => $message->content
    ));
    echo view('widgets/comment_user_info', array(
        'name' => $message->from_name,
        'location' => $message->from_location
    ));
    echo view('input/securitytoken');
    
    echo "<div style='float:right'>";   
    echo "<br />";
    echo "<a href='{$message->get_url()}'>".__('canceledit'). "</a>";
    echo "</div>";    
    
    echo view('input/submit', array('value' => __('savechanges')));        
?>
</div>