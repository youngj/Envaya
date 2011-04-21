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
        
        
        $news = $org->get_widget_by_class('News');
        if (!$news->guid)
        {
            $news->save();
        }
        
        $post = $news->new_widget_by_class('Post');
        $post->set_content(view('output/longtext', array('value' => $this->message));
        $post->save();
        $post->post_feed_items();
        
        $sms_request->reply("Posted news update to {$post->get_url()} . To delete, reply UNDO.");            
        $sms_request->reset_state();            
        $sms_request->set_state('undo_post', $post->guid);
    }    
}
