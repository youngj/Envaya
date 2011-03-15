<?php
    $widget = $vars['widget'];
    $relationship = $vars['relationship'];
      
    ob_start();
?>
<form method='POST' action='<?php echo $widget->get_edit_url() ?>?action=save_relationship'>
<?php

    echo view('widgets/network_edit_relationship_form', array('relationship' => $relationship));

    echo view('input/submit', array(
        'name' => '_save',
        'trackDirty' => true, 
        'value' => __('savechanges'),
    ));

?>
</form>

<?php
    $content = ob_get_clean();   
    echo view('section', array('header' =>  __('network:edit_relationship'), 'content' => $content));
?>