<?php
    $widget = $vars['widget'];
    $relationship = $vars['relationship'];
    
    $org = $relationship->get_subject_organization();
      
    ob_start();
?>
<form method='POST' action='<?php echo $widget->get_edit_url() ?>?action=edit_relationship'>
<?php
    
echo view('input/securitytoken');
echo view('input/hidden', array('name' => 'guid', 'value' => $relationship->guid));

if (!$org)
{
?>

<table class='inputTable' style='margin:0 auto'>
<?php echo view('widgets/network_relationship_fields', array('relationship' => $relationship)); ?>
</table>    
<?php
}
else
{
    echo view_entity($org);
}

echo "<div style='padding-top:5px'><em>".sprintf(__('network:describe_relationship'), escape($relationship->get_subject_name()))."</em></div>";
echo view('input/tinymce', array('name' => 'content', 'track_dirty' => true, 'value' => $relationship->content));

    echo view('input/submit', array(
        'name' => '_save',
        'track_dirty' => true, 
        'value' => __('savechanges'),
    ));

?>
</form>

<?php
    $content = ob_get_clean();   
    echo view('section', array('header' =>  __('network:edit_relationship'), 'content' => $content));
?>