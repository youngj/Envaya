<?php

class Action_Discussion_Invite extends Action
{
    function before()
    {
        $this->require_editor();       

        $org = $this->get_org();
        if (!$org->is_approved())
        {
            SessionMessages::add_error(__('noaccess'));
            forward($this->get_topic()->get_url());
        }
    }

    function process_input()
    {
        $topic = $this->get_topic();
        $org = $topic->get_root_container_entity();
        $selected_emails = get_input_array('invited_emails');

        $invited_emails = $topic->get_metadata('invited_emails') ?: array();
        
        $invite_message = get_input('invite_message');
        
        $topic_url = $topic->get_url();
        if (strpos($invite_message, $topic_url) === false)
        {
            $invite_message = "$topic_url\n\n$invite_message";
        }
        
        $mail = Zend::mail(
            sprintf(__('discussions:invite_subject'), $org->name, $topic->subject),
            $invite_message
        );
        
        $new_invited_emails = array();
        
        foreach ($selected_emails as $email)
        {
            if (!in_array($email, $invited_emails))
            {
                $invited_emails[] = $email;
                $new_invited_emails[] = $email;
                $mail->addBcc($email);
            }
        }
        
        if (sizeof($new_invited_emails) > 0)
        {        
            $mail->addTo(Config::get('admin_email'));
            send_mail($mail);
        
            $topic->set_metadata('invited_emails', $invited_emails);
            $topic->save();
            
            SessionMessages::add(__('discussions:invites_sent'));
        }
        else
        {
            SessionMessages::add_error(__('discussions:no_new_invites'));
            return $this->render();
        }
                
        
        forward($topic_url);
    }
    
    function render()
    {
        $topic = $this->get_topic();
                
        $cancelUrl = get_input('from') ?: $topic->get_url();
        PageContext::get_submenu('edit')->add_item(__("cancel"), $cancelUrl);        
        
        $this->page_draw(array(
            'title' => __('discussions:invite'),
            'content' => view("discussions/invite", array('topic' => $topic))
        ));
    }
}    