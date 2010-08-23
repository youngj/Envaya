<?php
    $widget = $vars['widget'];

    if ($widget->title || !in_array($widget->widget_name, Widget::get_default_names()))
    {
?>
<div class='input'>
    <label><?php echo __("widget:title") ?></label>
    <?php echo view("input/text", array(
        'internalname' => 'title',
        'trackDirty' => true,        
        'value' => $widget->title)); 
    ?>
</div>
<?php
    }
?>