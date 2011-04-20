<div class='section_content padded'>
<?php
    $widget = $vars['widget'];

?>
<form action='<?php echo $widget->get_base_url() ?>/add_widget' method='POST'>
<?php 
    echo view('input/securitytoken'); 

    echo view('widgets/edit_section_title');    

    echo view('input/hidden', array(
        'name' => 'uniqid',
        'value' => uniqid("",true)
    ));

    echo view('focus', array('id' => 'title')); 
    echo view('input/tinymce', array(
        'name' => 'content',
        'trackDirty' => true
    ));
    echo view('input/submit', array('trackDirty' => true, 'value' => __('widget:create_section'))); 
?>
</form>
</div>