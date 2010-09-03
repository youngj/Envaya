<?php
    $report = $vars['report'];
    $status = $report->status;
    
    if ($status == ReportStatus::Draft)
    {
        echo view('output/confirmlink', array(
            'href' => "{$report->get_url()}/set_status?status=".ReportStatus::Submitted,
            'text' => __("report:set_submitted"),
            'is_action' => true,
        ));
    }
    else if ($status == ReportStatus::Submitted)
    {            
        echo view('output/confirmlink', array(
            'href' => "{$report->get_url()}/set_status?status=".ReportStatus::Approved,
            'text' => __("report:approve"),
            'is_action' => true,
        ))."<br />";            
        
        echo view('output/confirmlink', array(
            'href' => "{$report->get_url()}/set_status?status=".ReportStatus::Draft,
            'text' => __("report:set_draft"),
            'is_action' => true,
        ));            
    }
    else if ($status == ReportStatus::Approved)
    {        
        echo view('output/confirmlink', array(
            'href' => "{$report->get_url()}/set_status?status=".ReportStatus::Submitted,
            'text' => __("report:undo_approve"),
            'is_action' => true,
        ));                    
    }