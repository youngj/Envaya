<?php

class ReportDefinition extends Entity
{
    static $subtype_id = T_report_definition;
    static $table_name = 'report_definitions';
    static $table_attributes = array(
        'handler_class' => '',
        'name' => '',
    );
    
    function get_title()
    {
        return $this->name;
    }    
    
    static function get_handler_options()
    {
        $options = array();
        $files = get_library_files(__DIR__."/reporthandler");
        foreach ($files as $file)
        {
            if (preg_match('/(\w+)\.php$/', $file, $matches))
            {
                $classname = "ReportHandler_$matches[1]";
                $handler = new $classname();                
                $options[get_class($handler)] = $handler->name;
            }            
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
}