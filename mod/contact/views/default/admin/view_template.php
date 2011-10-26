<?php
    $template = $vars['template'];
?>
<div class='section_content padded'>
<?php 
    echo view('admin/view_template_filters', array('template' => $template));   
    echo $vars['content'];
?>
</div>
