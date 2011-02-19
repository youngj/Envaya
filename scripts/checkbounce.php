<?php

/*
 * Checks for recent bounce emails in the 'email_from' inbox.
 * 
 * Prints a list of bounced email addresses and the reason why 
 * the email was rejected by the destination SMTP server.
 *
 * Assumes that the IMAP server is accessible on an unencrypted 
 * connection at localhost:143. Stunnel should be configured 
 * to forward connections on that port to the real IMAP 
 * server, e.g. with these lines in stunnel.conf:
 
[imaps]
accept  = 127.0.0.1:143
connect = imap.gmail.com:993
 
 */

require_once "scripts/cmdline.php";
require_once "engine/start.php";

function get_imap_inbox()
{
    $imap = new Net_IMAP('localhost',143);
    $imap->login(Config::get('email_from'),Config::get('email_pass'));
    $imap->selectMailbox("INBOX");
    return $imap;
}

function parse_bounce_email($bounce_email)
{
    if (preg_match('/<([^>]+)>: (([^\-]|\-[^\-])+)/', $bounce_email, $matches))
    {
        $email = $matches[1];
        $reason = $matches[2];
                        
        $reason = preg_replace('/\s+/'," ",$reason);
        
        $reason = preg_replace('/host [^\s]+ said: /','', $reason);
        $reason = str_replace('554 delivery error: dd ' , '', $reason);                
        $reason = preg_replace('/\(in reply to ([\w\s]+) command\)/' , '', $reason);                        
        $reason = str_replace($email, '', $reason);
        $reason = str_replace('()', '', $reason);
        $reason = preg_replace('/550([\-\s]5.1.1 )?/', '', $reason);        
        $reason = preg_replace('/\[.*\]/', '', $reason);
        $reason = preg_replace('/\- mta[\w\.]+/', '', $reason);
        $reason = preg_replace('/Learn more at.*/', '', $reason);        
        $reason = str_replace('Sorry your message to  cannot be delivered.', '', $reason);
        $reason = str_replace("Please try double-checking the recipient's email address for typos or unnecessary spaces. ", '', $reason);
        $reason = str_replace('Requested action not taken: ','', $reason);
        $reason = trim($reason);
        
        return array('address' => $email, 'reason' => $reason);
    }
    else
    {
        return null;
    }

}

function query_bounces($since_time)
{
    $bounced_addresses = array();

    $imap = get_imap_inbox();

    $since_str = date('d-M-Y', $since_time);    
    $bounced_ids = $imap->search('(SINCE '.$since_str.' FROM "MAILER-DAEMON@smtp.com")');

    foreach ($bounced_ids as $bounced_id)
    {
        $body = $imap->getBody($bounced_id);
            
        $bounce_info = parse_bounce_email($body);
            
        if ($bounce_info)
        {
            $bounced_addresses[$bounce_info['address']] = $bounce_info['reason'];
        }
    }

    $imap->disconnect();
   
    return $bounced_addresses;
}

$bounce_info = query_bounces(time() - 24 * 60 * 60 * 30);

foreach ($bounce_info as $address => $reason)
{
    echo sprintf("%-30s %s\n", $address, $reason);
}