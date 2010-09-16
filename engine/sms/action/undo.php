<?php

class SMS_Action_Undo extends SMS_Action
{
    function execute($sms_request)
    {
        $org = $sms_request->get_org();                
        if (!$org)
        {
            return $sms_request->reply("Phone number not registered with Envaya.");
        }
        
        $undo_post = $sms_request->get_state('undo_post');
        if (!$undo_post)
        {            
            return $sms_request->reply("UNDO is not available at this time.");
        }
        
        $news_update = $org->query_news_updates()->where('e.guid = ?', $undo_post)->get();
        if (!$news_update)
        {
            return $sms_request->reply("Could not delete news update.");
        }
        
        $news_update->delete();
        $sms_request->reply("News update deleted successfully.");
        $sms_request->reset_state();            
    }    
}
