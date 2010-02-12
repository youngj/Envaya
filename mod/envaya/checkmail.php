<?php

    require_once(dirname(__FILE__)."/lib/Net/Socket.php");
    require_once(dirname(__FILE__)."/lib/Net/POP3.php");
    require_once(dirname(__FILE__)."/lib/Net/mime_parser.php");
    require_once(dirname(__FILE__).'/lib/Net/rfc822_addresses.php');
    require_once(dirname(dirname(dirname(__FILE__))) . '/engine/start.php');
    
    global $CONFIG;
           
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
        
        $entities = get_entities_from_metadata('email_code', $emailCode, 'group', 'organization');
        if (empty($entities))
        {
            echo "no matching organization";
            return false;
        }      
        
        $subject = $decoded[0]["Headers"]["subject:"];        
        
        $org = $entities[0];
                
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
        
        if (empty($textEmail))
        {   
            echo "could not find text body of email";
            return false;
        }
        $blog = new ElggObject();
        $blog->subtype = "blog";
        $blog->owner_guid = $org->owner_guid;
        $blog->container_guid = $org->guid;    
        $blog->access_id = 2; //public              
        $blog->title = $subject;
        $blog->description = $textEmail;    
        
        if (!$blog->save()) 
        {
            echo "could not save blog post";
            return false;
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
            handle_message($pop3->getMsg($i));

            // $pop3->deleteMsg($i);
        }      
        
    }
    echo "</pre>";
   
    $pop3->disconnect();
    echo "done!";

?>