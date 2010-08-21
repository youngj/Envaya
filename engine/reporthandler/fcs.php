<?php

class ReportHandler_FCS extends ReportHandler
{
    static $view_name = 'reports/fcs';
    
    function save($report)
    {       
        $report->get_field('full_name')->value = get_input('full_name');
        $report->save();
    }
}