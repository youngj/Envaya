<?php

class Action_Admin_SendEmailTemplate extends Action
{
    function before()
    {
        $this->require_admin();
    }
     
    function process_input()
    {
        $email = EmailTemplate::get_by_guid(get_input('email'));
        if (!$email)
        {
            return $this->not_found();
        }
                
        $org_guids = get_input_array('orgs');
        $numSent = 0;
        foreach ($org_guids as $org_guid)
        {       
            $org = Organization::get_by_guid($org_guid);

            if ($email->can_send_to($org))
            {
                $numSent++;
                $email->send_to($org);
            }
        }
        SessionMessages::add("sent $numSent emails");
        forward(get_input('from') ?: "/admin/batch_email?email={$email->guid}");
    }

    function render()
    {
        $email = EmailTemplate::get_by_guid(get_input('email'))
            ?: EmailTemplate::query()->where('active<>0')->get();
        if (!$email)
        {
            return $this->not_found();
        }
        
        $org_guids = get_input_array('orgs');
        if ($org_guids)
        {
            $orgs = Organization::query()->where_in('guid', $org_guids)->filter();
        }
        else
        {         
            $orgs = Organization::query()
                ->where('approval > 0')
                ->where("email <> ''")
                ->where('(notifications & ?) > 0', Notification::Batch)
                ->where("not exists (select * from outgoing_mail where email_guid = ? and user_guid = users.guid)", $email->guid)
                ->order_by('guid')
                ->limit(50)
                ->filter(); 
        }

        $this->page_draw(array(
            'title' => __('email:batch'),
            'content' => view('admin/batch_email', array('email' => $email, 'orgs' => $orgs)),
        ));        
    }
}    