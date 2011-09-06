<?php

/*
 * Replying to SMS messages from contact_phone_number will send a notification email to the site administrators.
 */
class SMS_Controller_Contact extends SMS_Controller
{       
    public function execute($message)
    {
        $request = $this->request;
        
        $body = "$message\n\n";        
    
        $from_number = $request->get_from_number();
        $to_number = $request->get_to_number();
        
        $body .= "Phone Number: {$from_number}\n\n";
    
        $sms = OutgoingSMS::query()
            ->where('to_number = ?', $from_number)
            ->where('from_number = ?', $to_number)
            ->order_by('id desc')
            ->get();
            
        if ($sms)
        {
            $body .= "In reply to:\n{$sms->message}\n\n";
            
            if ($sms->to_name)
            {
                $body .= "Name: {$sms->to_name}\n\n";
            }
            $body .= "Sent: ".friendly_time($sms->time_sent)."\n\n";
        }
            
        $mail = OutgoingMail::create("SMS Reply from {$from_number}", $body);        
        $mail->send_to_admin();
    }
}
