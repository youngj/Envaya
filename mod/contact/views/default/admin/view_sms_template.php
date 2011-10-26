<?php    
    $template = $vars['template'];

    ob_start();
    
    echo view('admin/preview_sms_template', array('template' => $template));
    echo "<br /><a href='{$template->get_url()}/send'>".sprintf(__('contact:send_template'), __('contact:sms'))."</a>";    
    
    $content = ob_get_clean();
    
    echo view('admin/view_template', array('template' => $template, 'content' => $content));