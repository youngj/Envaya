<?php
    $widget = $vars['widget'];
    $org = $widget->get_container_entity();   
        
    ?>

<div class='input'>
    <label><?php 
    
        $labelCode = "widget:{$widget->widget_name}:label";
        $label = __($labelCode);
        if ($label != $labelCode)
        {
            echo $label;
        }
    ?></label>
    <?php
        $helpCode = "widget:{$widget->widget_name}:help";
        $help = __($helpCode);
        if ($help != $helpCode)
        {
            echo "<div class='help'>$help</div>";
        }
        else
        {
            echo "<br />";
        }
    ?>
    <?php echo view("input/tinymce", array(
        'name' => 'content',
        'autoFocus' => true,
        'trackDirty' => true,
        'valueIsHTML' => $widget->has_data_type(DataType::HTML),
        'value' => $widget->content)); ?>

</div>
