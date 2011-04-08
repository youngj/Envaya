<?php
    $org = $vars['org'];
    $user = Session::get_loggedin_user();  
  
    ob_start();
    
?>
<form method='POST' action='<?php echo $org->get_url(); ?>/topic/new'>
<div class='input'>
<label><?php echo __('discussions:subject'); ?></label><br />
<?php 
    echo view('input/text', array('name' => 'subject')); 
?>
</div>
<?php 
    echo view('input/tinymce', array('name' => 'content'));    
    echo view('discussions/user_info');
    
    $widget = $org->get_widget_by_class('WidgetHandler_Discussions');    
    
    echo "<div style='float:right'><br /><br />";    
    echo "<a href='{$widget->get_url()}'>".__('discussions:back_to_topics'). "</a>";
    echo "</div>";

    echo view('input/hidden', array(
        'name' => 'uuid',
        'value' => uniqid("",true)
    ));
    
    echo view('input/securitytoken');
    echo view('input/submit', array('value' => __('publish')));
 ?>
</form>

<script type='text/javascript'>
(function(){
    document.forms[0].uuid.value = new Date().getTime() + "." + Math.random();
})();
</script>

<?php
    echo view('focus', array('name' => 'subject'));

    $content = ob_get_clean();
    
    echo view('section', array(
        'header' => __('discussions:add_topic'), 
        'content' => $content
    ));
