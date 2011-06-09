<?php

/*
 * State machine that collects information about references to the _media directory from a stream of parser tokens.
 */
 
define('S_URLREF_IGNORE_NEXT', 1);

class StateMachine_URLRef extends StateMachine
{
    public $url_refs = array(/* url => referencing_paths */);    
       
    function get_next_state($token, $type, $line)
    {
        switch ($this->cur_state)
        {
            case S_INIT:
                if ($type == T_CONSTANT_ENCAPSED_STRING
                || $type == T_INLINE_HTML
                || $type == T_ENCAPSED_AND_WHITESPACE)
                {
                    // by convention, all URLs in our source code 
                    // are generated from relative URIs that begin with a slash (/)                    
                    if (strpos($token, '/') !== false
                        && preg_match_all('#[\"\'](/[\w\-\./]+)([\"\'\?\#]|$)#', $token, $matches, PREG_OFFSET_CAPTURE))
                    {
                        foreach ($matches[1] as $match_offset)
                        {
                            $match = $match_offset[0];
                            
                            // ignore _media URLs, which are handled by StateMachine_MediaRef
                            if (strpos($match, '/_media/') === 0)
                                continue;
                            
                            $match_line = $line + static::get_line_offset($token, $match_offset[1]);                           
                            $this->url_refs[$match][] = "{$this->cur_path}:$match_line";
                        }
                    }
                    return S_INIT;
                }
                else if ($token == '.' || $type == T_VARIABLE || $token == '}')
                {
                    return S_URLREF_IGNORE_NEXT;
                }
                return S_INIT;
            case S_URLREF_IGNORE_NEXT:
                return S_INIT;
        }
    }
}
