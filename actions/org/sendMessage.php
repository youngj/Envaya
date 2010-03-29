<?php
    gatekeeper();
    action_gatekeeper();
	
    $user = get_loggedin_user();
    $recipient_guid = (int)get_input("recipient_guid");
        
    $recipient = get_entity($recipient_guid);

    if (!$recipient)
    {
        register_error(elgg_echo("message:invalid_recipient"));   
        forward();
    }
    else 
    {
        $subject = get_input('subject');
        if (!$subject)
        {
            action_error(elgg_echo("message:subject_missing"));               
        }
        
        $message = get_input('message');
        if (!$message)
        {
            action_error(elgg_echo("message:message_missing"));               
        }
    
        $headers = array(
            'To' => $recipient->getNameForEmail(),
            'From' => $user->getNameForEmail(),
            'Reply-To' => $user->getNameForEmail(),
            'Bcc' => $user->getNameForEmail(),
        );        
        
        send_mail($recipient->email, $subject, $message, $headers);    
    
        system_message(elgg_echo("message:sent"));  
        
        forward($recipient->getURL());
    }