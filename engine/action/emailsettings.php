<?php

class Action_EmailSettings extends Action
{
    private function verify_access($email, $code, $subscriptions)
    {
        Permission_Public::require_any();
    
        if (!$email || $code != EmailSubscription::get_email_fingerprint($email) || sizeof($subscriptions) == 0)
        {
            throw new RedirectException(__("email:invalid_url"), "/pg/login");
        }
    }

    function process_input()
    {
        $email = Input::get_string('email');
        $code = Input::get_string('code');
        $language = Input::get_string('language');
        
        $all_subscription_ids = Input::get_array('subscriptions');
        $enabled_subscription_ids = Input::get_array('enabled_subscriptions');
        
        $subscriptions = EmailSubscription::query()
            ->where('email = ?', $email)
            ->where_in('guid', $all_subscription_ids)
            ->show_disabled(true)
            ->filter();
        
        $this->verify_access($email, $code, $subscriptions);

        foreach ($subscriptions as $subscription)
        {
            $subscription->set_status(
                in_array($subscription->guid, $enabled_subscription_ids) ?
                    Entity::Enabled : Entity::Disabled                
            );            
            $subscription->language = $language;
            $subscription->save();                        
        }
        
        LogEntry::create('email:change_subscriptions', null, $email);
        
        SessionMessages::add(__('email:notifications_changed'));

        $this->redirect();
    }

    function render()
    {
        $email = Input::get_string('e');
        $code = Input::get_string('c');
        $id = Input::get_string('id');
        
        $offset = Input::get_int('offset');
        
        $limit = 15;
        $show_more = false;
        
        $query = EmailSubscription::query()
            ->where('email = ?', $email)
            ->show_disabled(true)
            ->order_by('tid')
            ->limit($limit, $offset);
        
        if ($id)
        {
            $query->where('guid = ?', $id);
            $show_more = true;
        }        
        
        $count = $query->count();
        
        $subscriptions = $query->filter();        
        
        $this->verify_access($email, $code, $subscriptions);
        
        $this->page_draw(array(
            'title' => __("email:settings"),
            'content' => view('account/email_settings', array(
                'email' => $email, 
                'limit' => $limit,
                'count' => $count,
                'offset' => $offset,
                'subscriptions' => $subscriptions,
                'show_more' => $show_more
            ))
        ));
    }
}