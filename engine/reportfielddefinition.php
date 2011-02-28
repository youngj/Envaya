<?php

/*
 * Represents the structure of one field in a report definition,
 * including e.g. the type of input field, default value, and label text.
 */
class ReportFieldDefinition
{    
    public $label;
    public $help;
    public $default_value;
    public $custom_view;
    public $export_value = true;
    public $input_type = 'input/text';
    public $input_args;
    public $output_type = 'output/text';
    public $output_args;
    public $view_args;
    public $auto_value;
    public $auto_update;
    
    function __construct($args)
    {
        foreach ($args as $key => $value)
        {
            $this->$key = $value;
        }
    }
    
    function get_html_name($field)
    {
        return "field_{$field->name}";
    }
    
    function get_input_value($field)
    {
        $input_name = $this->get_html_name($field);
        return get_input($input_name);
    }
    
    function get_exported_values($field)
    {
        return array($field->name => $field->view_input());
    }    
    
    function render_edit($field)
    {    
        if ($this->custom_view)
        {
            return $this->render_custom_view($field, true);
        }    
        else
        {            
            $res = view($this->input_type, $this->get_input_args($field));       
             
            $res .= view('reports/auto_value', array(
                'field_name' => $field->name, 
                'auto_value' => $this->auto_value,
                'auto_update' => $this->auto_update,
            ));
            
            $res .= view('input/hidden', array(
                'name' => 'fields[]',
                'value' => $field->name
            )); 
            
            return $res;
        }
    }
    
    function render_view($field)
    {    
        if ($this->custom_view)
        {
            return $this->render_custom_view($field);
        }               
        else if (!$field->is_blank())
        {    
            return view($this->output_type, $this->get_output_args($field));
        }
        else
        {
            return view('reports/blank_response');
        }    
    }
    
    private function render_custom_view($field, $edit = false)
    {
        return view("reports/{$this->custom_view}", array('field' => $field, 'report' => $field->get_report(), 'edit' => $edit));    
    }            
    
    private function add_args(&$args, $arg_group)
    {
        if (is_array($arg_group))
        {
            foreach ($arg_group as $k => $v)
            {
                $args[$k] = $v;
            }
        }    
    }

    protected function get_input_args($field)
    {
        $input_name = $this->get_html_name($field);
    
        $input_args = array(
            'name' => $input_name,
            'trackDirty' => true,
            'value' => !$field->is_blank() ? $field->value : $this->default_value,
        );
        
        $this->add_args($input_args, $this->view_args);
        $this->add_args($input_args, $this->input_args);
        return $input_args;
    }
    
    protected function get_output_args($field)
    {
        $output_args = array('value' => $field->value);
        
        $this->add_args($output_args, $this->view_args);
        $this->add_args($output_args, $this->output_args);
        return $output_args;
    }    
}