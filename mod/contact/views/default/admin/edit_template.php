<div class='padded'>
<?php
    $template = $vars['template'];
?>
<form method='POST' action='<?php echo $template->get_url(); ?>/edit'>
<?php 
    echo view('input/securitytoken');
    echo view('admin/edit_template_filters', array('template' => $template));

    echo $vars['content'];
    
    echo view('admin/template_placeholders');

    echo view('input/alt_submit', array(
        'name' => "delete",
        'id' => 'widget_delete',
        'track_dirty' => true,
        'confirm' => __('areyousure'),
        'value' => __('delete')
    ));

    echo view('input/submit', array(
        'value' => __('save')));
?>
</form>
</div>