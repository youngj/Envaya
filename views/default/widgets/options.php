<?php 

$widget = $vars['widget'];
?>

<form action='<?php echo $widget->get_base_url() ?>/options' method='POST'>
<?php echo view('input/securitytoken') ?>
<div class='padded'>
<div class='input'>
<label><?php echo __('widget:title'); ?></label><br />
<?php
echo view('input/text', array(
    'name' => 'title',
    'value' => $widget->title
));
?>
</div>

<div class='input'>
<label><?php echo __('widget:subtype_id'); ?></label><br />
<?php
echo view('input/text', array(
    'name' => 'subtype_id',
    'value' => $widget->subtype_id
));
?>
</div>

<div class='input'>
<label><?php echo __('widget:handler_arg'); ?></label><br />
<?php
echo view('input/text', array(
    'name' => 'handler_arg',
    'value' => $widget->handler_arg
));
?>
</div>

<table style='float:right;font-size:10px;'>

<?php 
    $sibling_widgets = $widget->get_container_entity()->query_menu_widgets()->filter();

    foreach ($sibling_widgets as $w)
    {
        echo "<tr><td>{$w->get_menu_order()}</td><td style='padding-left:5px;'><a href='{$w->get_base_url()}/options'>".escape($w->get_title())."</a></td></tr>";
    }
?>
</table>
<div class='input'>
<label><?php echo __('widget:in_menu'); ?></label><br />
<?php
echo view('input/radio', array(
    'name' => 'in_menu',
    'options' => yes_no_options(),
    'value' => $widget->in_menu ? 'yes' : 'no',
));
?>
</div>
<div class='input'>
<label><?php echo __('widget:menu_order'); ?></label><br />
<?php
echo view('input/text', array(
    'name' => 'menu_order',
    'value' => $widget->menu_order,
    'style' => 'width:100px'
));
?>
</div>
<div style='clear:both'></div>
</div>

<?php
echo view('input/submit', array(    
    'value' => __('savechanges')
));


?>
</div>
</form>