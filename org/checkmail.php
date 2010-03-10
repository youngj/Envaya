<?php

    global $CONFIG;

    require_once("{$CONFIG->path}engine/lib/Net/Socket.php");
    require_once("{$CONFIG->path}engine/lib/Net/POP3.php");
    require_once("{$CONFIG->path}engine/lib/Net/mime_parser.php");
    require_once("{$CONFIG->path}engine/lib/Net/rfc822_addresses.php");
               
    function handle_message($msg)
    {
        $mime = new mime_parser_class;
        $mime->mbox = 0;    
        $mime->decode_bodies = 1;    
        $mime->ignore_syntax_errors = 1;
        $mime->track_lines = 1;    
                        
        $parameters=array(
            'Data'=>$msg
        );            

        if (!$mime->Decode($parameters, $decoded))
        {
            echo "mime error";
            return false;
        }

        $to = $decoded[0]["Headers"]["to:"];
        if (!preg_match('/post\+(\w+)@envaya\.org/i',$to,$matches))
        {        
            echo "invalid address";
            return false;
        }
                        
        $emailCode = $matches[1];
        
        $org = ElggUser::getUserByEmailCode($emailCode);
        if (!$org)
        {
            echo "no matching organization";
            return false;
        }      
        
        $subject = $decoded[0]["Headers"]["subject:"];        
                
        if (!$mime->Analyze($decoded[0], $results))
        {                      
            echo 'MIME message analyse error: '.$mime->error."\n";
            continue;
        }
        
        $textEmail = '';
        if ($results['Type'] == 'text')
        {
            $textEmail = $results['Data'];
        }
        else if (!empty($results['Alternative']))
        {
            foreach ($results['Alternative'] as $alt)
            {                
                if ($alt['Type'] == 'text')
                {
                    $textEmail = $alt['Data'];
                    break;
                }    
            }    
        }
        
        $textEmail = trim("$subject\n\n$textEmail");
       
        if (empty($textEmail))
        {   
            echo "could not find text body of email";
            return false;
        }
        
        $blog = new NewsUpdate();
        $blog->owner_guid = $org->guid;
        $blog->container_guid = $org->guid;    
        $blog->title = '';
        $blog->content = $textEmail;
        
        if (!$blog->save()) 
        {
            echo "could not save blog post";
            return false;
        }
        
        if (isset($results["Attachments"]))
        {
            foreach ($results['Attachments'] as $attachment)
            {
                if ($attachment['Type'] == 'image')
                {
                    $blog->setImage($attachment['Data']);
                    break; 
                }
            }
        }                
        
        return true;    
    }

    echo "<pre>";
    $pop3 = new Net_POP3();
    if ($pop3->connect())
    {
        $pop3->login("post@envaya.org",$CONFIG->email_pass);
                
        for($i = 1; $i <= $pop3->numMsg(); $i++) 
        {                
            set_time_limit(60);
        
            handle_message($pop3->getMsg($i));

            // $pop3->deleteMsg($i);
        }      
        
    }
    echo "</pre>";
   
    $pop3->disconnect();
    echo "done!";

?>