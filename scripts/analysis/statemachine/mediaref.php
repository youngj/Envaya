<?php

/*
 * State machine that collects information about references to the _media directory from a stream of parser tokens.
 */

class StateMachine_MediaRef extends StateMachine
{
    public $media_refs = array(/* media_path => referencing_paths */);
    
    function get_next_state($token, $type, $line)
    {
        switch ($this->cur_state)
        {
            case S_INIT:        
                if ($type == T_CONSTANT_ENCAPSED_STRING
                || $type == T_INLINE_HTML
                || $type == T_ENCAPSED_AND_WHITESPACE)
                {
                    if (strpos($token, '_media') !== false
                        && preg_match_all('#_media/[\w\-/]+\.[\w\.]+#', $token, $matches, PREG_OFFSET_CAPTURE))
                    {
                        foreach ($matches[0] as $match_offset)
                        {
                            $match = $match_offset[0];
                            $match_line = $line + static::get_line_offset($token, $match_offset[1]);
                            $this->media_refs[$match][] = "{$this->cur_path}:$match_line";
                        }
                    }
                    return S_INIT;
                }
                return S_INIT;
        }
    }
}
