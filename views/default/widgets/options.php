<?php 

$widget = $vars['widget'];
?>

<form action='<?php echo $widget->getBaseURL() ?>/save_options' method='POST'>
<?php echo elgg_view('input/securitytoken') ?>
<div class='padded'>
<div class='input'>
<label><?php echo __('widget:title'); ?></label><br />
<?php
echo elgg_view('input/text', array(
    'internalname' => 'title',
    'value' => $widget->title
));
?>
</div>

<div class='input'>
<label><?php echo __('widget:handler'); ?></label><br />
<?php
echo elgg_view('input/text', array(
    'internalname' => 'handler_class',
    'value' => $widget->handler_class
));
?>
</div>

<div class='input'>
<label><?php echo __('widget:handler_arg'); ?></label><br />
<?php
echo elgg_view('input/text', array(
    'internalname' => 'handler_arg',
    'value' => $widget->handler_arg
));
?>
</div>

<table style='float:right;font-size:10px;'>

<?php 
    foreach ($widget->getContainerEntity()->getAvailableWidgets() as $w)
    {
        echo "<tr><td>{$w->getMenuOrder()}</td><td style='padding-left:5px;'><a href='{$w->getBaseURL()}/options'>".escape($w->getTitle())."</a></td></tr>";
    }
?>
</table>
<div class='input'>
<label><?php echo __('widget:in_menu'); ?></label><br />
<?php
echo elgg_view('input/radio', array(
    'internalname' => 'in_menu',
    'options' => yes_no_options(),
    'value' => $widget->in_menu ? 'yes' : 'no',
));
?>
</div>
<div class='input'>
<label><?php echo __('widget:menu_order'); ?></label><br />
<?php
echo elgg_view('input/text', array(
    'internalname' => 'menu_order',
    'value' => $widget->menu_order,
    'js' => 'style="width:100px"'
));
?>
</div>
<div style='clear:both'></div>
</div>

<?php
echo elgg_view('input/submit', array(
    'internalname' => 'submit',
    'value' => __('savechanges')
));


?>
</div>
</form>