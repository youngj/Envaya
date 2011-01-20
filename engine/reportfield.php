<?php

class ReportField extends Model
{
    static $table_name = 'report_fields';
    static $table_attributes = array(
        'name' => '',
        'value' => '',
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
        
    function view_input()
    {   
        return $this->get_definition()->render_view($this);        
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