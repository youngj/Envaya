<?php

class Action_Admin_ChangeOrgApproval extends Action
{
    function before()
    {
        $this->require_admin();
    }
     
    function process_input()
    {
        $guid = (int)get_input('org_guid');
        $org = Organization::get_by_guid($guid);

        if (!$org)
        {
            throw new NotFoundException();
        }
        
        $approvedBefore = $org->is_approved();

        $org->approval = (int)get_input('approval');

        $approvedAfter = $org->is_approved();

        $org->save();

        if (!$approvedBefore && $approvedAfter)
        {
            if ($org->email)
            {
                OutgoingMail::create(
                    __('register:approval_email:subject', $org->language),
                    view('emails/org_approved', array('org' => $org))
                )->send_to_user($org);
            }
            
            $primary_phone = $org->get_primary_phone_number();
            if ($primary_phone && PhoneNumber::can_send_sms($primary_phone))
            {
                SMS_Service_News::create_outgoing_sms(
                    $primary_phone,
                    strtr(__('register:approval_sms', $org->language),
                        array(
                            '{url}' => $org->get_url(),
                            '{login_url}' => abs_url('/pg/login')
                        )
                    )
                )->send();
            }
        }
        
        // send any emails from this user that were held pending approval
        $held_emails = OutgoingMail::query()->where('status = ?', OutgoingMail::Held)->where('from_guid = ?', $org->guid)->filter();
        foreach ($held_emails as $held_email)
        {
            $held_email->enqueue();
        }

        SessionMessages::add(__('approval:changed'));

        $this->redirect($org->get_url());
    }
}