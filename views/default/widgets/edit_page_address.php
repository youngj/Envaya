<?php
    $container = $vars['container'];
?>
<div class='input'>
<label><?php echo __('widget:address'); ?></label>
<div class='websiteUrl'>
<?php echo abs_url($container->get_url()) . "/page/" . view('input/text', array(
    'name' => 'widget_name', 
    'id' => 'widget_name', 
    'value' => @$vars['value'],
    'style' => "width:200px"
)); 
?>
</div>
</div>
