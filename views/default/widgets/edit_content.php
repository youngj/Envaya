<?php
    $widget = $vars['widget'];
    $org = $widget->getContainerEntity();   
        
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
        'internalname' => 'content',
        'trackDirty' => true,
        'allowCustomHTML' => $widget->allowUnsafeHTML(),
        'valueIsHTML' => $widget->hasDataType(DataType::HTML),
        'value' => $widget->content)); ?>

</div>
