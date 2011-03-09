<?php

/*
 * Base class for rendering a specific report definition, overridden by subclasses in
 * reporthandler/ . Eventually this will be replaced by storing report definitions in
 * the database instead of code.
 */
class ReportHandler
{
    function view($report, $args = null)
    {
        $res = '';
        
        if (!$args)
        {
            $args = array();
        }
        $args['edit'] = false;
        
        foreach ($this->get_sections() as $section_id => $section)
        {   
            $res .= view('reports/view_section', array(
                'section_id' => $section_id, 
                'section' => $section,
                'report' => $report,
                'content' => $this->render_section($report, $section, $args)
            ));
        }
        return $res;
    }
    
    function render_section($report, $section, $args)
    {
        $view = @$args['section_view'] ?: 'reports/default_section';
        
        $args['report'] = $report;
        $args['section'] = $section;
        
        return view($view, $args);
    }
    
    function edit($report, $args = null)
    {
        $section_id = (int)get_input('section') ?: 1;        
        
        $sections = $this->get_sections();
        $section = $sections[$section_id];        
                
        if (!$args)
        {
            $args = array();
        }
        $args['edit'] = true;
                
        return view('reports/edit_section',             
            array(
                'content' => $this->render_section($report, $section, $args),
                'report' => $report,
                'section_id' => $section_id,
                'section' => $section
            )
        );
    }    
      
}