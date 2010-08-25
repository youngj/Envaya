<?php

class ReportHandler_FCS extends ReportHandler
{
    static $view_name = 'reports/fcs';
    
    function view($report)
    {
        return view('reports/fcs_view', array('report' => $report));
    }
    
    function edit($report)
    {
        $section = (int)get_input('section') ?: 1;        
        $content = $this->get_edit_content($report, $section);        
        
        return view('reports/edit_section',             
            array(
                'content' => $content,
                'section' => $section,
                'max_section' => 2                
            )
        );
    }    
    
    function get_edit_content($report, $section)
    {
        $args = array('report' => $report, 'section' => $section, 'edit' => true);        
        $view_name = "reports/fcs_section$section";
        return view($view_name, $args);
    }
    
    function save($report)
    {       
        $section = (int)get_input('section') ?: 1;
        
        if ($section == 1)
        {
            $report->get_field('full_name')->value = get_input('full_name');
        }
        else if ($section == 2)
        {
            $report->get_field('amount')->value = get_input('amount');
        }
        
        $report->save();
        
        $next_section = get_input('next_section');        
        if ($next_section)
        {
            system_message(__("report:section_saved"));
            forward($report->get_edit_url()."?section=$next_section");
        }
        else
        {
            system_message(__("report:saved"));
            forward($report->get_url());
        }
    }
}