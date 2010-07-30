<?php
    $widget = $vars['widget'];

    if ($widget->title || !in_array($widget->widget_name, Widget::getDefaultNames()))
    {
?>
<div class='input'>
    <label><?php echo __("widget:title:edit") ?></label>
    <?php echo elgg_view("input/text", array(
        'internalname' => 'title',
        'trackDirty' => true,        
        'value' => $widget->title)); 
    ?>
</div>
<?php
    }
?>