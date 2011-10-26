<?php   
    $subscription = $vars['subscription'];

    $header = "<p>".sprintf(__('contact:select_sms'), escape($subscription->get_recipient_description()))."</p>";
    
    echo view('admin/subscription_templates', array(
        'subscription' => $subscription,
        'header' => $header,
        'query' => SMSTemplate::query()->order_by('time_created desc'),    
    ));