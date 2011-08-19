<?php

class IncomingMail
{
    public $subject;
    public $to;
    public $from;
    public $text;
    
    // map of regexes that match tag of "to" address => handler function
    static $tag_actions = array(
        '#^comment(?P<guid>\d+)$#' => 'reply_comment',
        '#^message(?P<guid>\d+)$#' => 'reply_discussion_message'
    );

    function __construct()
    {
    
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
    
        if (!$tag)
        {
            error_log("address {$this->to} did not have a valid reply tag");
            return false;
        }
        
        foreach (static::$tag_actions as $regex => $fn)
        {
            if (preg_match($regex, $tag, $match))
            {
                return call_user_func_array(array($this, $fn), array($match));
            }
        }                
        
        error_log("address {$this->to} did not match any rules");
        return false;
    }    
    
    function reply_discussion_message($match)
    {
        $guid = $match['guid'];
        
        $message = DiscussionMessage::get_by_guid($guid, true);
        if (!$message)
        {
            error_log("invalid message guid $guid");
            return false;
        }
        
        $topic = $message->get_container_entity();
        if (!$topic)
        {
            error_log("invalid container for message guid $guid");
            return false;
        }
        
        $parsed_address = EmailAddress::parse_address($this->from);
        
        $reply = new DiscussionMessage();
        $reply->container_guid = $topic->guid;    
        $reply->from_name = @$parsed_address['name'];
        $reply->subject = $this->subject;
        $reply->from_location = "via email";
        $reply->from_email = @$parsed_address['address'];
        $reply->set_content(nl2br(static::strip_quoted_text($this->text)));
        $reply->time_posted = time();
        $reply->save();

        $topic->refresh_attributes();
		$topic->save();
        
        error_log("added message {$reply->guid}");

        return true;
    }

    function reply_comment($match)
    {
        $guid = $match['guid'];
        
        $comment = Comment::get_by_guid($guid, true);
        if (!$comment)
        {
            error_log("invalid comment guid $guid");
            return false;
        }
        
        $widget = $comment->get_container_entity();
        if (!$widget)
        {
            error_log("invalid container for comment guid $guid");
            return false;
        }
        
        $parsed_address = EmailAddress::parse_address($this->from);
        
        $reply = new Comment();
        $reply->container_guid = $widget->guid;    
        $reply->name = @$parsed_address['name'];
        $reply->location = "via email";
        $reply->content = static::strip_quoted_text($this->text);
        $reply->save();
        
        $widget->refresh_attributes();
		$widget->save();
        
        error_log("added comment {$reply->guid}");
        
        return true;
    }    
}