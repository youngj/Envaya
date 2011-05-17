<?php
    $topic = $vars['topic'];
?>
<form method='POST' action='<?php echo $topic->get_url(); ?>/add_message'>
<?php 
    echo view('input/tinymce', array('name' => 'content', 'trackDirty' => true, 'autoFocus' => true));        
    echo view('discussions/user_info');
    echo view('input/securitytoken');
    echo view('input/uniqid');    
    echo view('input/submit', array('value' => __('discussions:publish_message')));    
 ?>
</form>
