<?php   
    $subscription = $vars['subscription'];

    $header = "<p>".sprintf(__('contact:select_email'), 
        "<a href='mailto:".escape($subscription->email)."'>".escape($subscription->email)."</a>")."</p>";
    
    $query = EmailTemplate::query()->order_by('time_created desc');

    echo view('admin/subscription_templates', array(
        'subscription' => $subscription,
        'header' => $header,
        'query' => $query,    
    ));