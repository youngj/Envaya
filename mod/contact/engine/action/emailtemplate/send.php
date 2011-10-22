<?php

class Action_EmailTemplate_Send extends Action
{
    function before()
    {
        $this->require_admin();
    }
     
    function process_input()
    {
        $email = $this->get_email();
                
        $subscription_guids = get_input_array('subscriptions');
        $numSent = 0;
        foreach ($subscription_guids as $subscription_guid)
        {       
            $subscription = EmailSubscription::get_by_guid($subscription_guid);

            if ($email->can_send_to($subscription))
            {
                $numSent++;
                $subscription->send_notification(ContactTemplate::Sent, $email);
            }
        }
        $email->update();
        
        SessionMessages::add("Queued $numSent emails for delivery.");
                
        $from = get_input('from');
        if ($from)
        {
            $this->redirect($from);
        }
        else if ($email->query_potential_recipients()->is_empty())
        {
            $this->redirect($email->get_url());        
        }
        else
        {
            $this->redirect("{$email->get_url()}/send");
        }
    }

    function render()
    {
        $email = $this->get_email();
        
        $subscription_guids = get_input_array('subscriptions');
        if ($subscription_guids)
        {
            $subscriptions = EmailSubscription::query()->where_in('guid', $subscription_guids)->filter();
        }
        else
        {         
            $subscriptions = $email->query_potential_recipients()
                ->order_by('guid')
                ->limit(Config::get('contact:max_recipients'))
                ->filter(); 
        }
        
        PageContext::get_submenu('edit')->add_item(__('cancel'), get_input('from') ?: $email->get_url());
        
        $this->page_draw(array(
            'title' => __('contact:send_email'),
            'header' => view('admin/email_header', array(
                'email' => $email,
                'title' => __('email:send')
            )),            
            'content' => view('admin/batch_email', array('email' => $email, 'subscriptions' => $subscriptions)),
        ));        
    }
}    