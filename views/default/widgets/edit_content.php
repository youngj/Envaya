<?php
    $widget = $vars['widget'];
    $org = $widget->get_container_entity();   
            
    ?>

<div class='input' style='padding-bottom:0px'>
<?php     
    $labelCode = "widget:{$widget->widget_name}:label";
    $label = __($labelCode);
    if ($label != $labelCode)
    {
        echo "<label>$label</label>";
    }
    
    $helpCode = "widget:{$widget->widget_name}:help";
    $help = __($helpCode);
    if ($help != $helpCode)
    {
        echo "<div class='help'>$help</div>";
    }

    echo view("input/tinymce", array(
        'name' => 'content',
        'autoFocus' => true,
        'trackDirty' => true,
        'saveDraft' => true,
        'entity' => $widget,
        'value' => $widget->content
    )); 
?>
</div>
