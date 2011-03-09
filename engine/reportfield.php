<?php

/*
 * Represents one organization's response to one question on a report,
 * and provides methods for rendering the response or input field.
 */
class ReportField extends Model
{
    static $table_name = 'report_fields';
    static $table_attributes = array(
        'name' => '',        
        'value' => '',
        'access' => 0,
        'value_type' => 0,
        'report_guid' => 0
    );   
    
    function get($name)
    {
        $value = parent::get($name);

        if ($name == 'value')
        {
            return VariantType::decode_value($value, $this->attributes['value_type']);
        }
        return $value;
    }

    function set($name, $value)
    {
        if ($name == 'value')
        {           
            $value = VariantType::encode_value($value, $this->attributes['value_type']);            
        }
        parent::set($name, $value);
    }         
    
    function get_definition()
    {
        return $this->get_report()->get_field_definition($this->name);
    }
        
    function label()
    {
        return $this->get_definition()->label;
    }

    function help()
    {
        return $this->get_definition()->help;
    }
    
    private $report;
    
    function set_report($report)
    {
        $this->report = $report;
        $this->report_guid = $report->guid;
    }
    
    function get_report()
    {
        return $this->report ?: get_entity($this->report_guid);
    }
    
    function is_blank()
    {
        $value = $this->value;
        return !$value && $value !== 0 && $value !== '0';
    }
    
    function view_html()
    {        
        return view('reports/view_field', array('field' => $this));
    }

    function edit_html()
    {        
        return view('reports/edit_field', array('field' => $this));
    }
        
    function can_view($user = null)
    {
        if ($this->access == ReportAccess::OpenToPublic)
        {
            return true;
        }
        
        if (!$user)
        {
            $user = Session::get_loggedin_user();
        }
        
        if ($user)
        {
            $report = $this->get_report();

            if ($user->guid == $report->container_guid) 
                return true;
            if ($user->guid == $report->get_report_definition()->container_guid) 
                return true;
            if ($user->admin)
                return true;                
        }
        
        return false;
    }
        
    function view_input()
    {       
        if ($this->can_view())
        {    
            return $this->get_definition()->render_view($this);        
        }
        else
        {
            return view('reports/confidential_field');
        }
    }
    
    function edit_input()
    {   
        return $this->get_definition()->render_edit($this);        
    }    
    
    function get_input_value()
    {
        return $this->get_definition()->get_input_value($this);
    }
}