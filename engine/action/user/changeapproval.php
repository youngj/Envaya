<?php

class Action_User_ChangeApproval extends Action
{
    function before()
    {
        Permission_ChangeUserApproval::require_for_entity($this->get_user());
    }
     
    function process_input()
    {
        $user = $this->get_user();
    
        $approvedBefore = $user->is_approved();

        $user->approval = (int)get_input('approval');

        $approvedAfter = $user->is_approved();

        $user->save();

        if (!$approvedBefore && $approvedAfter && ($user instanceof Organization))
        {
            if ($user->email)
            {
                OutgoingMail::create(
                    __('register:approval_email:subject', $user->language),
                    view('emails/org_approved', array('org' => $user))
                )->send_to_user($user);
            }
            
            $primary_phone = $user->get_primary_phone_number();
            if ($primary_phone && PhoneNumber::can_send_sms($primary_phone))
            {
                SMS_Service_News::create_outgoing_sms(
                    $primary_phone,
                    strtr(__('register:approval_sms', $user->language),
                        array(
                            '{url}' => abs_url($user->get_url()),
                            '{login_url}' => secure_url('/pg/login')
                        )
                    )
                )->send();
            }
        }
        
        // send any emails from this user that were held pending approval
        $held_emails = OutgoingMail::query()
            ->where('status = ?', OutgoingMail::Held)
            ->where('from_guid = ?', $user->guid)
            ->filter();
        
        foreach ($held_emails as $held_email)
        {
            $held_email->enqueue();
        }

        SessionMessages::add(__('approval:changed'));

        $this->redirect($user->get_url());
    }
}
