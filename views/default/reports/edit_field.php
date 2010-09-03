<?php
    $field = $vars['field'];
?>
<div class='input'>
<div><label><?php echo $field->label(); ?></label></div>

<?php
    echo "<div class='help'>".$field->help()."</div>"; 
?>
<?php echo $field->edit_input(); ?>
</div>