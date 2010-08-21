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
   
    function getDateText()
    {
        return friendly_time($this->time_created);
    }
    
    function getURL()
    {
        return $this->getContainerEntity()->getURL()."/report/".$this->guid;
    }
    
    function getReportDefinition()
    {
        return get_entity($this->report_guid);
    }
    
    function getHandler()
    {
        try
        {
            $handlerCls = new ReflectionClass($this->getReportDefinition()->handler_class);
            return $handlerCls->newInstance();                        
        }
        catch (ReflectionException $ex)
        {        
            return new ReportHandler_Invalid();
        }        
    }

    function renderView()
    {
        return $this->getHandler()->view($this);
    }

    function saveInput()
    {
        return $this->getHandler()->save($this);
    }    
    
    function renderEdit()
    {
        return $this->getHandler()->edit($this);
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