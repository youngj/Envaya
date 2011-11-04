<?php
    $widget = $vars['widget'];
    $org = $widget->get_container_entity();   
            
    ?>

<div class='input' style='padding-bottom:0px'>
<?php     
    $heading = $widget->get_edit_heading();
    if ($heading)
    {
        echo "<label>$heading</label>";
    }
    
    $help = $widget->get_edit_help(); 
    if ($help)
    {
        echo "<div class='help'>$help</div>";
    }

    echo view("input/tinymce", array(
        'name' => 'content',
        'autoFocus' => true,
        'track_dirty' => true,
        'saveDraft' => true,
        'entity' => $widget,
        'value' => $widget->content
    )); 
?>
</div>
