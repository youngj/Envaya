<?php

/*
 * Base class for rendering a specific report definition, overridden by subclasses in
 * reporthandler/ . Eventually this will be replaced by storing report definitions in
 * the database instead of code.
 */
class ReportHandler
{
    function view($report)
    {
        $res = '';
        
        foreach ($this->get_sections() as $section_id => $section)
        {   
            $res .= view('reports/view_section', array(
                'section_id' => $section_id, 
                'section' => $section,
                'report' => $report,
                'content' => $this->render_section($report, $section)
            ));
        }
        return $res;
    }
    
    function render_section($report, $section, $edit = false)
    {
        $view = @$section['view'] ?: 'default_section';
        
        return view("reports/$view", 
            array('report' => $report, 'section' => $section, 'edit' => $edit)
        );
    }
    
    function edit($report)
    {
        $section_id = (int)get_input('section') ?: 1;        
        
        $sections = $this->get_sections();
        $section = $sections[$section_id];        
                
        return view('reports/edit_section',             
            array(
                'content' => $this->render_section($report, $section, true),
                'report' => $report,
                'section_id' => $section_id,
                'section' => $section
            )
        );
    }    
      
}