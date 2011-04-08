<?php
    $topic = $vars['topic'];
    $org = $topic->get_container_entity();
    $user = Session::get_loggedin_user();  
    
    ob_start();
?>
<form method='POST' action='<?php echo $topic->get_url(); ?>/add_message'>

<?php 
    echo view('input/tinymce', array('name' => 'content', 'autoFocus' => true));        
    echo view('discussions/user_info');
?>

<div style='float:right'>
<br />
<br />
<a href='<?php echo $topic->get_url(); ?>'><?php echo __('discussions:back_to_messages'); ?></a>
</div>

<?php
    echo view('input/securitytoken');
    
    echo view('input/hidden', array(
        'name' => 'uuid',
        'value' => uniqid("",true)
    ));
    
    echo view('input/submit', array('value' => __('discussions:publish_message')));    
 ?>
</form>

<script type='text/javascript'>
(function(){
    document.forms[0].uuid.value = new Date().getTime() + "." + Math.random();
})();
</script>

<?php
    $content = ob_get_clean();
        
    echo view('section', array(
        'header' => escape($topic->subject), 
        'content' => $content
    ));
    
?>