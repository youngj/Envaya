<?php

/*
 * State machine that collects information about definitions of CSS IDs and classes.
 */

class StateMachine_CSSDef extends StateMachine
{
    public $css_defs = array(/* .class_name or #id  => path */);
    
    function set_path($path)
    {
        parent::set_path($path);
        if (strpos($path, 'css/') === false)
        {
            $this->cur_state = S_IGNORE_FILE;
        }
    }
    
    function get_next_state($token, $type, $line)
    {
        switch ($this->cur_state)
        {
            case S_INIT:        
                if ($type == T_INLINE_HTML)
                {
                    if (preg_match_all('/[#\.][a-zA-Z][\w\-]+/', $token, $matches, PREG_OFFSET_CAPTURE))
                    {
                        foreach ($matches[0] as $match_offset)
                        {
                            $match = $match_offset[0];
                            
                            // ignore probable color definitions like #fff, or file extensions
                            if (preg_match('/^#(([a-f0-9]{3})|([a-f0-9]{6}))$/i', $match)
                            || preg_match('/^\.(jpg|png|gif)$/i', $match))
                                continue;
                            
                            $match_line = $line + static::get_line_offset($token, $match_offset[1]);
                            $this->css_defs[$match] = "{$this->cur_path}:$match_line";
                        }
                    }
                }
                return S_INIT;
            case S_IGNORE_FILE:
                return $this->error($token, $type, $line);                                
        }
    }
}
