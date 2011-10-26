<?php
    $footer = "<a href='/admin/contact/sms/add'>Add new SMS template</a>";
    
    echo view('admin/list_templates', array(
        'query' => SMSTemplate::query()->order_by('time_created desc'),
        'footer' => $footer
    ));
