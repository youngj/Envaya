<?php
    $org = $vars['org'];
?>
<div class='input'>
<label><?php echo __('widget:address'); ?></label>
<div class='websiteUrl'>
<?php echo $org->get_url() . "/page/" . view('input/text', array(
    'name' => 'widget_name', 
    'id' => 'widget_name', 
    'value' => @$vars['value'],
    'js' => "style='width:200px'"
)); 
?>
</div>
</div>
