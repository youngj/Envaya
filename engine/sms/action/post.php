<?php

class SMS_Action_Post extends SMS_Action
{
    protected $message;

    function __construct($message)
    {
        $this->message = $message;
    }

    function execute($sms_request)
    {
        $org = $sms_request->get_org();
        if (!$org)
        {
            return $sms_request->reply("Phone number not registered with Envaya.");
        }
        
        $news_update = new NewsUpdate();
        $news_update->container_guid = $org->guid;
        $news_update->set_content(view('output/longtext', array('value' => $this->message));
        $news_update->save();
        $news_update->post_feed_items();
        
        $sms_request->reply("Posted news update to {$news_update->get_url()} . To delete, reply UNDO.");            
        $sms_request->reset_state();            
        $sms_request->set_state('undo_post', $news_update->guid);
    }    
}
