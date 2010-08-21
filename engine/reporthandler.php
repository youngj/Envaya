<?php

class ReportHandler
{
    static $view_name = '';
    static $fields = array();

    function view($report)
    {
        return view(static::$view_name, array('report' => $report));
    }
    
    function edit($report)
    {
        return view(static::$view_name, array('report' => $report, 'edit' => true));
    }

    function save($report)
    {
    }    
}