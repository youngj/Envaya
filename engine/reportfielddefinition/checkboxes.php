<?php

class ReportFieldDefinition_Checkboxes extends ReportFieldDefinition
{    
    function __construct($args)
    {
        $args['input_type'] = 'input/checkboxes';
        $args['output_type'] = 'output/checkboxes';
        
        parent::__construct($args);        
    }

    function get_input_value($field)
    {
        $input_name = $this->get_html_name($field);
        return get_input_array($input_name);
    }    
}