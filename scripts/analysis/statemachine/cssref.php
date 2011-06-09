<?php

/*
 * State machine that collects information about references to CSS classes and IDs 
 * from a stream of parser tokens.
 */

define('S_CSSREF_KEY', 1);
define('S_CSSREF_ARROW', 2);

class StateMachine_CSSRef extends StateMachine
{
    public $css_refs = array(/* .class_name or #id => referencing_paths */);
    
    private $cur_css_type = null;
        
    function add_css_ref($ref, $ref_type, $line)
    {
        $path = "{$this->cur_path}:$line";
    
        if ($ref_type == 'id')
        {
            $this->css_refs["#$ref"][] = $path;
        }
        else // ref is space-delimited list of css class names
        {
            foreach(explode(' ', $ref) as $css_class)
            {
                $this->css_refs[".$css_class"][] = $path;
            }
        }
    }
              
    function get_next_state($token, $type, $line)
    {
        switch ($this->cur_state)
        {
            case S_INIT:        
                if ($type == T_CONSTANT_ENCAPSED_STRING
                || $type == T_INLINE_HTML
                || $type == T_ENCAPSED_AND_WHITESPACE)
                {
                    if ((strpos($token, 'id=') !== false || strpos($token, 'class=') !== false)
                        && preg_match_all('#(id|class)=[\"\']([\w\-\s]+)[\"\']#', 
                            $token, $matches, PREG_OFFSET_CAPTURE))
                    {
                        $num_matches = sizeof($matches[0]);
                        for ($i = 0; $i < $num_matches; $i++)
                        {   
                            $ref_type = $matches[1][$i][0];
                            $css_ref = $matches[2][$i][0];
                            $offset = $matches[2][$i][1];
                            
                            $match_line = $line + static::get_line_offset($token, $offset);
                            $this->add_css_ref($css_ref, $ref_type, $match_line);
                        }

                        return S_INIT;
                    }
                }
            
                if ($type == T_CONSTANT_ENCAPSED_STRING)
                {
                    if ($token == "'id'" || $token == '"id"')
                    {
                        $this->cur_css_type = 'id';
                        return S_CSSREF_KEY;
                    }
                    if ($token == "'class'" || $token == '"class"')
                    {
                        $this->cur_css_type = 'class';
                        return S_CSSREF_KEY;
                    }
                }
                return S_INIT;
            case S_CSSREF_KEY:
                if ($token == T_DOUBLE_ARROW)
                {
                    return S_CSSREF_ARROW;
                }
                return S_INIT;
            case S_CSSREF_ARROW:
                if ($type == T_CONSTANT_ENCAPSED_STRING)
                {               
                    $ref = eval("return $token;");                    
                    $this->add_css_ref($ref, $this->cur_css_type, $line);                    
                    return S_INIT;
                }
                return S_INIT;
        }
    }
}
