<?php

class ReportHandler_FCS extends ReportHandler
{ 
    public $num_sections = 2;
    public $report_code = 'fcs';
    public $name = 'FCS - Working Narrative Report';
    
    public function get_field_args($report = null)
    {
        $org = $report ? $report->get_container_entity() : (new Organization());
    
        $fields = array(
            'full_name' => array(
                'label' => 'A. Full name of organization',
                'help' => 'As written in the contract between your organization and The Foundation for Civil Society',
                'default' => $org->name
            ),
            'other_name' => array(
                'label' => 'B: Name you regularly use',
                'help' => 'Any other name or abbreviation used to describe your organization',
            ),
            'project_name' => array(
                'label' => 'C: Full name of project',
                'help' => 'As written in your contract',
            ),
            'reference_num' => array(
                'label' => 'D. Contract reference number',
                'help' => 'FCS xxx as written in your contract',
            ),
            'report_period' => array(
                'label' => 'E. Period covered by this report',
                'help' => 'Give the dates and the quarter(s) the reporting period covers. For example, if your project started on August 10,  2001, your quarter one report covers the period August 10 to September 30, 2001. Your quarter two report covers the period October 1 to December 31, 2001. If your project started on August 10, 2001 and is for two years, your end-of-year report covers quarters 1 to 4 from August 10, 2001 to June 30th, 2002.',
                'label_only' => true
            ),
            'report_dates' => array(
                'label' => "Dates",
                'input_args' => array('js' => "style='width:150px;'"),
            ),
            'report_quarters' => array(
                'label' => 'Quarters',
                'input_args' => array('js' => "style='width:150px;'"),
            ),                            
            'project_coordinator' => array(
                'label' => 'F. Project Coordinator',
                'help' => 'Full Name and Contact Information',
                'input_type' => 'input/longtext',
                'input_args' => array('js' => "style='height:80px;'"),
                'output_type' => 'output/longtext',    
            ),
            'thematic_areas' => array(
                'label' => 'A. What thematic area(s) of the Foundation for Civil Society does your project address?',
                'help' => 'As written in Part 1.2 of your application',
                'input_type' => 'input/checkboxes',
                'options' => array(
                    'policy' => "Policy Engagement",
                    'capacity' => "Civil Society Capacity Strengthening",
                    'governance' => "Governance and Accountability",
                ),
                'output_type' => 'output/checkboxes',    
            ),
            'project_description' => array(
                'label' => 'B. Briefly describe what your project will do and how it addresses the thematic area selected above',
                'help' => 'As written in Part 1.4 of your application',
                'input_type' => 'input/longtext',
                'output_type' => 'output/longtext',    
            ),
            'project_regions' => array(
                'label' => 'D. Which Region(s) and District(s) are they located in?',
                'help' => 'Where are the people your project supports living?',
                'input_type' => 'input/checkboxes',
                'output_type' => 'output/checkboxes',    
                'options' => regions_in_country('tz'),                
            )
        );
                
        $constituency_args = array('input_args' => array('js' => 'style="width:80px"'));                
                
        foreach (array('widows','elderly','refugees','poor','orphans','unemployed','hiv_aids','children','youth','homeless','disabled','other')
            as $constituency)
        {
            $fields[$constituency."_female"] = $constituency_args;
            $fields[$constituency."_male"] = $constituency_args;
        }
        
        $fields['other_details'] = array(
            'label' => 'If other, provide details.'
        );
        
        return $fields;        
    }    
}