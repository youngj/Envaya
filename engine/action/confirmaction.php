<?php

class Action_ConfirmAction extends Action
{    
    function render()
    {
        validate_security_token(true);       
        
        $message = get_input('message');
        $ok_url = get_input('ok');
        $cancel_url = get_input('cancel');

        // hack to handle redirect_back or redirect_back_error by going back to previous page
        if (Session::get('messages'))
        {
            forward($cancel_url);
        }        
        
        $this->page_draw(array(
            'title' => $message,
            'content' => view("output/confirm_action", array('ok_url' => $ok_url, 'cancel_url' => $cancel_url))
        ));        
    }
}    