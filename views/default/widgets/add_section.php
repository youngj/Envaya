<div class='section_content padded'>
<?php
    $widget = $vars['widget'];

?>
<form action='<?php echo $widget->get_base_url() ?>/add' method='POST'>
<?php 
    echo view('input/securitytoken'); 
    echo view('widgets/edit_section_title');    
    echo view('input/uniqid');            
    echo view('focus', array('id' => 'title')); 
    echo view('widgets/edit_initial_content');    
    echo view('input/submit', array('trackDirty' => true, 'value' => __('widget:create_section'))); 
?>
</form>
</div>