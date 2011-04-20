<?php
    $topic = $vars['topic'];
    $org = $topic->get_container_entity();
    $user = Session::get_loggedin_user();  
    
    echo "<div class='section_content padded'>";
    
    echo "<h3><a href='{$topic->get_url()}' style='color:#333'>".escape($topic->subject)."</a></h3>";
    echo "<div style='font-weight:bold;padding-bottom:8px'>".__('discussions:add_message')."</div>";

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
        'name' => 'uniqid',
        'value' => uniqid("",true)
    ));
    
    echo view('input/submit', array('value' => __('discussions:publish_message')));    
 ?>
</form>

<script type='text/javascript'>
(function(){
    document.forms[0].uniqid.value = new Date().getTime() + "." + Math.random();
})();
</script>

<?php
    echo "</div>";
?>