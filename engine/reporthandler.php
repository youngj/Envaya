<?php

class ReportHandler
{
    public $num_sections = 1;
    public $report_code = '';    

    function view($report)
    {
        $res = '';
        for ($i = 1; $i <= $this->num_sections; $i++)
        {   
            $res .= view('reports/view_section', array(
                'section' => $i, 
                'report' => $report,
                'content' => view("reports/{$this->report_code}/section$i", array('report' => $report))
            ));
        }
        return $res;
    }
    
    function edit($report)
    {
        $section = (int)get_input('section') ?: 1;        
        $content = $this->get_edit_content($report, $section);        
        
        return view('reports/edit_section',             
            array(
                'content' => $content,
                'report' => $report,
                'section' => $section,
            )
        );
    }    
    
    function get_edit_content($report, $section)
    {
        $args = array('report' => $report, 'section' => $section, 'edit' => true);        
        $view_name = "reports/{$this->report_code}/section$section";
        return view($view_name, $args);
    }    
    
}