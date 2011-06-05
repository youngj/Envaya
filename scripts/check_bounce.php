<?php

/*
 * Checks for recent bounce emails in the 'email_from' inbox.
 * 
 * Prints a list of bounced email addresses and the reason why 
 * the email was rejected by the destination SMTP server.
 * 
 */

require_once "scripts/cmdline.php";
require_once "start.php";

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
        
        $date = null;
        if (preg_match('/(\w+\s\w+)\s20\d\d/', $bounce_email, $date_matches))
        {
            $date = $date_matches[1];
        }        
        
        return array(
            'address' => $email, 
            'reason' => $reason,            
            'date' => $date
        );
    }
    else
    {
        return null;
    }

}

function query_bounces($since_time)
{
    $bounced_addresses = array();

    echo "hi!\n";
    
    $imap = Zend::imap();
    $imap->login(Config::get('email_from'), Config::get('email_pass'));
    $imap->select("INBOX");
    
    echo "logged in, searching...\n";

    $since_str = date('d-M-Y', $since_time);    
    $bounced_ids = $imap->search(array(
        'SINCE', $since_str,
        'FROM', $imap->escapeString("MAILER-DAEMON@smtp.com")
    ));

    echo "search complete\n";
    echo sizeof($bounced_ids);
    echo "\n";
    
    foreach ($bounced_ids as $bounced_id)
    {
        $body = $imap->fetch('RFC822.TEXT', $bounced_id);
            
        $bounce_info = parse_bounce_email($body);
            
        if ($bounce_info)
        {
            $address = $bounce_info['address'];
            if (isset($bounced_addresses[$address]))
            {
                $bounced_addresses[$address]['date'] .= ", " . $bounce_info['date'];
            }
            else
            {
                $bounced_addresses[$address] = $bounce_info;
            }
        }
    }

    $imap->logout();
   
    return $bounced_addresses;
}

$bounces_info = query_bounces(time() - 24 * 60 * 60 * 30);

foreach ($bounces_info as $address => $bounce_info)
{
    echo sprintf("%-30s %-10s %s\n", $address, $bounce_info['date'], $bounce_info['reason']);
}