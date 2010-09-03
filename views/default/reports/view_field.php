<?php
    $field = $vars['field'];
?>
<div class='input'>
<div><label><?php echo $field->label(); ?></label></div>
<?php echo $field->view_input(); ?>
</div>