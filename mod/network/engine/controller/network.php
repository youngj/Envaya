<?php

class Controller_Network extends Controller_User
{
    static $routes = array(        
        array(
            'regex' => '/(?P<action>\w+)\b',
        ),        
    );

    function action_relationship_emails_js()
    {    
        $this->require_site_editor();
    
        $this->set_content_type('text/javascript');
        
        $org = $this->get_org();
                
        $relationships = Relationship::query_for_user($org)
            ->where("subject_guid <> 0 OR subject_email <> ''")
            ->filter();
     
        $emails = array();
        
        foreach ($relationships as $relationship)
        {
            $email = $relationship->get_subject_email();
            if ($email)
            {        
                $emails[] = $email;
            }
        }
     
        $this->set_content(json_encode(array('emails' => $emails)));
    }       
}