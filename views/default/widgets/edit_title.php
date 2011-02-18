<?php
    $widget = $vars['widget'];

    if ($widget->title || !in_array($widget->widget_name, Widget::get_default_names()))
    {
?>
<div class='input'>
    <label><?php echo __("widget:title") ?></label><br />
    <?php echo view("input/text", array(
        'name' => 'title',
        'js' => "style='width:170px' maxlength='22'",
        'trackDirty' => true,        
        'value' => $widget->title)); 
    ?>
</div>
<?php
    }
?>