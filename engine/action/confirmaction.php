<?php

class Action_ConfirmAction extends Action
{    
    function render()
    {
        validate_security_token(true);       
        
        $message = get_input('message');
        $ok_url = get_input('ok');
        $cancel_url = get_input('cancel');

        // hack to handle forward_to_referrer or action_error by going back to previous page
        if (Session::get('messages'))
        {
            forward($cancel_url);
        }        
        
        $area1 = view("output/confirm_action", array('ok_url' => $ok_url, 'cancel_url' => $cancel_url));
       
        $body = view_layout("one_column", view_title($message), $area1);
        
        $this->page_draw($message,$body);  
    }
}    