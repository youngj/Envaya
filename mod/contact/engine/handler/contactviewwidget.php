<?php

class Handler_ContactViewWidget
{
    static function execute($vars)
    {
        $user = $vars['user'];
        
        if (Permission_UseAdminTools::has_for_entity($user))
        {
            $user_actions_menu = $vars['user_actions_menu'];
        
            $subscription = EmailSubscription_Contact::query_for_entity($user)->get();
            if ($subscription)
            {
                $user_actions_menu->add_link(
                    sprintf(__('contact:send_template'), __('contact:email')),
                    "/admin/contact/email/subscription/{$subscription->guid}");
            }
            
            $subscription = SMSSubscription_Contact::query_for_entity($user)->get();
            if ($subscription)
            {
                $user_actions_menu->add_link(
                    sprintf(__('contact:send_template'), __('contact:sms')),
                    "/admin/contact/sms/subscription/{$subscription->guid}");
            }
        }
    }
}