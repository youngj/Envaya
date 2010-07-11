<div class='padded section_content'>
<?php

    $widget = $vars['widget'];
    $org = $widget->getContainerEntity();


    ob_start();
?>
<div class='input'>
    <label><?php echo elgg_echo("widget:{$widget->widget_name}:label") ?></label>
    <?php
        $helpCode = "widget:{$widget->widget_name}:help";
        $help = elgg_echo($helpCode);
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

<?php
    $content = ob_get_clean();

    echo elgg_view("widgets/edit_form", array(
        'widget' => $widget,
        'body' => $content
    ));

?>
</div>