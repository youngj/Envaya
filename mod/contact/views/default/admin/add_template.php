<div class='padded'>
<form method='POST' action='<?php echo $vars['action'] ?>'>
<?php echo view('input/securitytoken') ?>
<?php
    echo view('admin/edit_template_filters', array('template' => $vars['template']));
    echo $vars['content'];
    echo view('admin/template_placeholders');    
    echo view('input/submit', array('value' => __('save')));
?>
</form>
</div>