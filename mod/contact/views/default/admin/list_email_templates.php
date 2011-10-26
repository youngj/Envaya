<?php
    $footer = "<a href='/admin/contact/email/add'>Add new email template</a>";
    
    echo view('admin/list_templates', array(
        'query' => EmailTemplate::query()->order_by('time_created desc'),
        'footer' => $footer
    ));