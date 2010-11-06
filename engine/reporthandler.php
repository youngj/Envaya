<?php

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
                'content' => view("reports/{$section['view']}", array('report' => $report))
            ));
        }
        return $res;
    }
    
    function edit($report)
    {
        $section_id = (int)get_input('section') ?: 1;        
        
        $sections = $this->get_sections();
        $section = $sections[$section_id];        
       
        $content = view("reports/{$section['view']}", 
            array('report' => $report, 'edit' => true)
        );
                
        return view('reports/edit_section',             
            array(
                'content' => $content,
                'report' => $report,
                'section_id' => $section_id,
                'section' => $section
            )
        );
    }    
      
}