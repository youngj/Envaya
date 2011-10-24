<?php

class IncomingMail
{
    public $subject;
    public $to;
    public $from;
    public $text;
    public $attachments = array();
    
    // map of regexes that match secure tag of "to" address => handler function
    static $tag_actions = array(
        '#^comment(?P<guid>\d+)$#' => array('EmailSubscription_Comments', 'handle_mail_reply'),
    );
    
    function __construct()
    {
    
    }
    
    static function add_tag_action($regex, $fn)
    {
        static::$tag_actions[$regex] = $fn;
    }
    
    function add_attachment($file)
    {
        $this->attachments[] = $file;
    }
    
    static function strip_quoted_text($text)
    {
        $lines = explode("\n", $text);
        
        $cleaned_lines = array();
        
        // apply heuristics to detect beginning of quoted text from previous email
        // e.g. lines starting with '>' character.
        foreach ($lines as $line)
        {
            if (preg_match('#^(>|___|(\-\-\-)|(Subject\:)|(To\:)|(From\:)|(Date\:))#', $line)
                || preg_match('#On\s.*\swrote\:(\s*)$#', $line))
            {
                break;
            }
            else
            {
                $cleaned_lines[] = $line;
            }        
        }
        
        $cleaned_text = trim(implode("\n", $cleaned_lines));
        
        return $cleaned_text;
    }
    
    function process()
    {
        $tag = EmailAddress::get_signed_tag($this->to);
    
        if ($tag)
        {
            foreach (static::$tag_actions as $regex => $fn)
            {
                if (preg_match($regex, $tag, $match))
                {
                    return call_user_func_array($fn, array($this, $match));
                }
            }
        }
        
        if ($this->to == Config::get('email_from')) // possible bounce email
        {
            return $this->handle_bounce();
        }        
        
        error_log("address {$this->to} did not match any rules");
        return false;
    }        

    static function clean_bounce_reason($reason, $to_address)
    {
        $reason = str_replace('smtp;' , '', $reason);
        $reason = str_replace('554 delivery error: dd ' , '', $reason);                
        $reason = preg_replace('/\(in reply to ([\w\s]+) command\)/' , '', $reason);                        
        $reason = str_replace($to_address, '', $reason);
        $reason = str_replace('()', '', $reason);
        $reason = preg_replace('/550([\-\s]5.1.1 )?/', '', $reason);        
        $reason = preg_replace('/\[.*\]/', '', $reason);
        $reason = preg_replace('/\- mta[\w\.]+/', '', $reason);
        $reason = preg_replace('/Learn more at.*/', '', $reason);        
        $reason = str_replace('Sorry your message to  cannot be delivered.', '', $reason);
        $reason = str_replace("Please try double-checking the recipient's email address for typos or unnecessary spaces. ", '', $reason);
        $reason = str_replace('Requested action not taken: ','', $reason);
        return trim($reason);            
    }
    
    function handle_bounce()
    {
        if (sizeof($this->attachments) == 0 || $this->attachments[0]['type'] != 'message/delivery-status')
        {
            error_log("invalid bounce email, missing message/delivery-status");
            return false;
        }
    
        $delivery_status = file_get_contents($this->attachments[0]['tmp_name']);
        
        $header_sets = explode("\r\n\r\n", $delivery_status);
        var_dump($header_sets);
        
        if (sizeof($header_sets) < 2)
        {
            error_log("invalid bounce email, invalid format for message/delivery-status");
            return false;
        }
        
        $headers = iconv_mime_decode_headers($header_sets[1], ICONV_MIME_DECODE_CONTINUE_ON_ERROR);
        
        $recipient = @$headers['Original-Recipient'];
        list($rfc822, $to_address) = explode(';', $recipient);        
        $to_address = trim($to_address);
        if (!$to_address)
        {
            error_log("invalid bounce email Original-Recipient: $recipient");
            return false;
        }
        
        $reason = static::clean_bounce_reason(@$headers['Diagnostic-Code'], $to_address);
                        
        // todo use message-id from original email to match bounce with OutgoingMail        
        $outgoing_mail = OutgoingMail::query()
            ->where('to_address = ?', $to_address)
            ->where('status = ?', OutgoingMail::Sent)
            ->where('time_sent > ?', timestamp() - 86400 * 5) 
            ->order_by('id desc')
            ->get();
            
        if (!$outgoing_mail)
        {
            error_log("bounce email to $to_address did not match any outgoing mail");
            return false;        
        }
        
        $outgoing_mail->status = OutgoingMail::Bounced;
        $outgoing_mail->error_message = $reason;
        $outgoing_mail->save();
        
        error_log("processed bounce email to $to_address");
    }
}