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
            return $this->not_found();
        }
        
        $approvedBefore = $org->is_approved();

        $org->approval = (int)get_input('approval');

        $approvedAfter = $org->is_approved();

        $org->save();

        if (!$approvedBefore && $approvedAfter && $org->email)
        {
            OutgoingMail::create(
                __('register:approval_email:subject', $org->language),
                view('emails/org_approved', array('org' => $org))
            )->send_to_user($org);
        }
        
        // send any emails from this user that were held pending approval
        $held_emails = OutgoingMail::query()->where('status = ?', OutgoingMail::Held)->where('from_guid = ?', $org->guid)->filter();
        foreach ($held_emails as $held_email)
        {
            $held_email->enqueue();
        }

        SessionMessages::add(__('approval:changed'));

        forward($org->get_url());
    }
}