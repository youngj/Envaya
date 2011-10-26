<?php    
    $template = $vars['template'];

    ob_start();
    echo view('admin/preview_email_template', array('template' => $template));    
    echo view('admin/email_template_statistics', array('template' => $template));
    echo "<br /><a href='{$template->get_url()}/send'>".sprintf(__('contact:send_template'), __('contact:email'))."</a>";    
    $content = ob_get_clean();
    
    echo view('admin/view_template', array('template' => $template, 'content' => $content));