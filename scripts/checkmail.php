<?php

    require_once("scripts/cmdline.php");
    
    require_once("engine/start.php");    
    require_once("vendors/mime_parser.php");
    require_once("vendors/rfc822_addresses.php");
               
    umask(0);           
               
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
            throw new DataFormatException("mime error");
        }

        $to = $decoded[0]["Headers"]["to:"];
        
        if (!preg_match('/\+(\w+)@envaya\.org/i',$to,$matches))
        {
            throw new DataFormatException("invalid address: $to");
        }
                        
        $emailCode = $matches[1];
                           
        $org = User::getByEmailCode($emailCode);
                
        if (!$org)
        {
            throw new InvalidParameterException("no matching organization: $emailCode");
        }      
        
        $subject = $decoded[0]["Headers"]["subject:"];        
                
        if (!$mime->Analyze($decoded[0], $results))
        {                      
            throw new DataFormatException('MIME message analyse error: '.$mime->error);
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
            throw new DataFormatException("could not find text body of email");
        }
        
        $blog = new NewsUpdate();
        $blog->owner_guid = $org->guid;
        $blog->container_guid = $org->guid;            
        $blog->content = $textEmail;        
        $blog->save();
        
        print_msg("saved news update {$blog->guid}");
        
        if (isset($results["Attachments"]))
        {
            foreach ($results['Attachments'] as $attachment)
            {
                if ($attachment['Type'] == 'image')
                {
                    // here we could save images
                    break; 
                }
            }
        }                
        
        return true;    
    }

    $pop3 = new Net_POP3();
    if ($pop3->connect())
    {
        $pop3->login(Config::get('post_email'),Config::get('email_pass'));
                
        print_msg("{$pop3->numMsg()} messages in mailbox");
                
        for($i = 1; $i <= $pop3->numMsg(); $i++) 
        {   
            print_msg("  processing message $i");
            try
            {
                handle_message($pop3->getMsg($i));
            }
            catch (DataFormatException $e) 
            {
                echo $e->getMessage()."\n";
            }
            catch (InvalidParameterException $e) 
            {
                echo $e->getMessage()."\n";
            }

            // $pop3->deleteMsg($i);
        }      
        
    }
   
    $pop3->disconnect();
    print_msg("done checking mail");
