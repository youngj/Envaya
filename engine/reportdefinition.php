<?php

class ReportDefinition extends Entity
{
    static $subtype_id = T_report_definition;
    static $table_name = 'report_definitions';
    static $table_attributes = array(
        'handler_class' => '',
        'name' => '',
    );
    static $handler_classes = array(
        'ReportHandler_FCS_Narrative',
    );
    
    private $handler = null;
    
    function get_title()
    {
        return $this->get_handler()->get_title();
    }    
    
    static function get_handler_options()
    {
        $options = array();
        foreach (static::$handler_classes as $classname)
        {
            $handler = new $classname();                
            $options[get_class($handler)] = $handler->get_title();
        }
    
        return $options;
    }
    
    function query_reports()
    {
        return Report::query()->where('report_guid = ?', $this->guid);
    }
    
    function query_approved()
    {
        return $this->query_reports()->where('status = ?', ReportStatus::Approved);
    }
    
    function get_handler()
    {
        if (!$this->handler)
        {    
            try
            {
                $handlerCls = new ReflectionClass($this->handler_class);
                $this->handler = $handlerCls->newInstance();                        
            }
            catch (ReflectionException $ex)
            {        
                $this->handler = new ReportHandler_Invalid();
            }        
        }
        return $this->handler;
    }
    
    function get_url()
    {
        return "{$this->get_container_entity()->get_url()}/reporting/{$this->guid}";
    }
    
    function get_field_definitions()
    {
        return $this->get_handler()->get_field_definitions();
    }
    
    function get_exported_field_definitions()
    {           
        $field_defs = array();

        foreach ($this->get_field_definitions() as $field_name => $field_def)
        {
            if ($field_def->export_value)
            {
                $field_defs[$field_name] = $field_def;
            }
        }
        return $field_defs;
    }
}