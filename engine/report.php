<?php

class Report extends Entity
{
    static $subtype_id = T_report;
    static $table_name = 'reports';
    static $table_attributes = array(
        'report_guid' => 0,
        'status' => 0,
    );      
    private $fields = null;
   
    function get_date_text()
    {
        return friendly_time($this->time_created);
    }
    
    function get_url()
    {
        return $this->get_container_entity()->get_url()."/report/".$this->guid;
    }

    function get_edit_url()
    {
        return $this->get_url()."/edit";
    }
    
    function get_report_definition()
    {
        return get_entity($this->report_guid);
    }
    
    function get_handler()
    {
        try
        {
            $handlerCls = new ReflectionClass($this->get_report_definition()->handler_class);
            return $handlerCls->newInstance();                        
        }
        catch (ReflectionException $ex)
        {        
            return new ReportHandler_Invalid();
        }        
    }

    function render_view()
    {
        return $this->get_handler()->view($this);
    }

    function save_input()
    {
        return $this->get_handler()->save($this);
    }    
    
    function render_edit()
    {
        return $this->get_handler()->edit($this);
    }
    
    function &get_fields()
    {
        if ($this->fields == null)
        {
            $fields = array();
            foreach (ReportField::query()->where('report_guid = ?', $this->guid)->filter() as $field)
            {
                $fields[$field->name] = $field;
            }
            $this->fields = $fields;
        }
        return $this->fields;
    }
    
    function get_field($name)
    {
        $fields = &$this->get_fields();
        if (!isset($fields[$name]))
        {
            $field = new ReportField();
            $field->name = $name;
            $field->report_guid = $this->guid;
            $fields[$name] = $field;
        }
        
        return $fields[$name];
    }
    
    function save()
    {
        parent::save();
    
        foreach ($this->get_fields() as $name => $field)
        {
            if ($field->dirty)
            {
                $field->save();
            }
        }
    }
    
}