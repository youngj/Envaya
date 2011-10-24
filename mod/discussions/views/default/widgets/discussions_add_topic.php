<?php
    $widget = $vars['widget'];
    $org = $widget->get_container_entity();
  
    ob_start();
    
?>
<form method='POST' action='<?php echo $widget->get_edit_url(); ?>?action=add_topic'>
<div class='input'>
<label><?php echo __('discussions:subject'); ?></label><br />
<?php 
    echo view('input/text', array('name' => 'subject')); 
?>
</div>
<?php 
    echo view('input/tinymce', array('name' => 'content'));    
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
