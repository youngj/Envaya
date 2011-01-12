<?php
    $field = $vars['field'];
?>
<div class='input'>
<div><label><?php echo $field->label(); ?></label></div>
<div class='report_field_<?php echo escape($field->name); ?>'><?php 
    echo $field->view_input(); 
?>
</div>
</div>