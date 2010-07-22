<?php
    $widget = $vars['widget'];
    $org = $widget->getContainerEntity();
?>

<div class='input'>
    <label><?php echo __("widget:{$widget->widget_name}:label") ?></label>
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
    <?php echo elgg_view("input/tinymce", array(
        'internalname' => 'content',
        'trackDirty' => true,
        'valueIsHTML' => $widget->hasDataType(DataType::HTML),
        'value' => $widget->content)); ?>

</div>
