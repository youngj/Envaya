<?php

class Action_ContactTemplate_Send extends Action
{
    function before()
    {
        Permission_SendMessage::require_for_root();
    }
     
    function process_input()
    {
        $subscription_class = $this->get_subscription_class();
    
        $template = $this->get_template();
                
        $subscription_guids = get_input_array('subscriptions');
        $numSent = 0;
        foreach ($subscription_guids as $subscription_guid)
        {       
            $subscription = $subscription_class::get_by_guid($subscription_guid);

            if ($template->can_send_to($subscription))
            {
                $numSent++;
                $subscription->send_notification(ContactTemplate::Sent, $template);
            }
        }
        $template->update();
        
        SessionMessages::add("Queued $numSent messages for delivery.");
                
        $from = get_input('from');
        if ($from)
        {
            $this->redirect($from);
        }
        else if ($template->query_potential_recipients()->is_empty())
        {
            $this->redirect($template->get_url());        
        }
        else
        {
            $this->redirect("{$template->get_url()}/send");
        }
    }

    function render()
    {
        $subscription_class = $this->get_subscription_class();
    
        $template = $this->get_template();
        
        $subscription_guids = get_input_array('subscriptions');
        if ($subscription_guids)
        {
            $subscriptions = $subscription_class::query()->where_in('guid', $subscription_guids)->filter();
        }
        else
        {         
            $subscriptions = $template->query_potential_recipients()
                ->order_by('tid')
                ->limit(Config::get('contact:max_recipients'))
                ->filter(); 
        }
        
        PageContext::get_submenu('top')->add_link(
            __('cancel'),
            get_input('from') ?: $template->get_url());
        
        $this->page_draw(array(
            'title' => sprintf(__('contact:send_template'), $this->get_type_name()),
            'header' => $this->get_header(array(
                'template' => $template,
                'title' => __('send')
            )),            
            'content' => view($this->controller->send_view, array(
                'template' => $template, 
                'subscriptions' => $subscriptions
            )),
        ));        
    }
}    