<?php

class ReportHandler_FCS_Narrative extends ReportHandler
{ 
    public $name = 'FCS - Narrative Report';    
    
    public function get_sections()
    {
        return array(
            1 => array(
                'title' => __('fcs:narrative:introduction'),
                'view' => 'fcs/narrative/introduction',
            ),
            2 => array(
                'title' => __('fcs:narrative:description'),
                'view' => 'fcs/narrative/description',
            ),
            3 => array(
                'title' => __('fcs:narrative:activities'),
                'view' => 'fcs/narrative/activities',
            ),
            4 => array(
                'title' => __('fcs:narrative:outcomes'),
                'view' => 'fcs/narrative/outcomes',
            ), 
            5 => array(
                'title' => __('fcs:narrative:lessons'),
                'view' => 'fcs/narrative/lessons',
            ),
            6 => array(
                'title' => __('fcs:narrative:challenges'),
                'view' => 'fcs/narrative/challenges',
            ),         
            7 => array(
                'title' => __('fcs:narrative:linkages'),
                'view' => 'fcs/narrative/linkages',
            ),
            8 => array(
                'title' => __('fcs:narrative:future_plans'),
                'view' => 'fcs/narrative/future_plans',
            ), 
            9 => array(
                'title' => __('fcs:narrative:beneficiaries'),
                'view' => 'fcs/narrative/beneficiaries',
            ),
            10 => array(
                'title' => __('fcs:narrative:events_attended'),
                'view' => 'fcs/narrative/events_attended',
            ),            
        );
    }
    
    public function get_field_args($report = null)
    {
        $org = $report ? $report->get_container_entity() : (new Organization());
    
        $fields = array(
            'full_name' => array(
                'label' => __('fcs:narrative:full_name'),
                'help' => __('fcs:narrative:full_name:help'),
                'default' => $org->name
            ),
            'other_name' => array(
                'label' => __('fcs:narrative:other_name'),
                'help' => __('fcs:narrative:other_name:help'),
            ),
            'project_name' => array(
                'label' => __('fcs:narrative:project_name'),
                'help' => __('fcs:narrative:project_name:help'),
            ),
            'reference_num' => array(
                'label' => __('fcs:narrative:reference_num'),
                'help' => __('fcs:narrative:reference_num:help'),
            ),
            'report_period' => array(
                'label' => __('fcs:narrative:report_period'),
                'help' => __('fcs:narrative:report_period:help'), 
                'label_only' => true
            ),
            'report_dates' => array(
                'label' => __('fcs:narrative:report_dates'),
                'input_args' => array('js' => "style='width:200px;'"),
            ),
            'report_quarters' => array(
                'label' => __('fcs:narrative:report_quarters'),
                'input_args' => array('js' => "style='width:80px;'"),
            ),                            
            'project_coordinator' => array(
                'label' => __('fcs:narrative:project_coordinator'),
                'help' => __('fcs:narrative:project_coordinator:help'),
                'input_type' => 'input/longtext',
                'input_args' => array('js' => "style='height:80px;'"),
                'output_type' => 'output/longtext',    
            ),
            'thematic_areas' => array(
                'label' => __('fcs:narrative:thematic_areas'),
                'help' => __('fcs:narrative:thematic_areas:help'),
                'input_type' => 'input/checkboxes',
                'output_type' => 'output/checkboxes',    
                'view_args' => array(
                    'options' => array(
                        'policy' => __('fcs:thematic_area:policy'),
                        'capacity' => __('fcs:thematic_area:capacity'),
                        'governance' => __('fcs:thematic_area:governance'),
                    ),                
                )
            ),
            'project_description' => array(
                'label' => __('fcs:narrative:project_description'),
                'input_type' => 'input/longtext',
                'output_type' => 'output/longtext',    
            ),
            'regions' => array(
                'label' => __('fcs:narrative:regions'), 
                'input_type' => 'input/grid',
                'output_type' => 'output/grid',
                'view_args' => array(
                    'columns' => array(
                        'region' => array(
                            'label' => __('fcs:area:region'),
                            'width' => 150,
                        ),
                        'district' => array(
                            'label' => __('fcs:area:district'),
                            'width' => 150,
                        ),
                        'ward' => array(
                            'label' => __('fcs:area:ward'), 
                            'width' => 150,
                        ), 
                        'villages' => array(
                            'label' => __('fcs:area:villages'),
                            'width' => 150,
                        ),
                        'total' => array(
                            'label' => __('fcs:area:total'),
                            'width' => 110,
                        ),                        
                    )
                )                
            ),
            'outputs' => array(
                'label' => __('fcs:narrative:outputs'),
                'input_type' => 'input/longtext',
                'input_args' => array('js' => "style='height:80px;'"),
                'output_type' => 'output/longtext',    
            ),            
            'planned_activities' => array(
                'label' => __('fcs:narrative:planned_activities'),
                'input_type' => 'input/longtext',
                'input_args' => array('js' => "style='height:80px;'"),
                'output_type' => 'output/longtext',    
            ),
            'achievements' => array(
                'label' => __('fcs:narrative:achievements'),
                'help' => __('fcs:narrative:achievements:help'),
                'input_type' => 'input/longtext',
                'input_args' => array('js' => "style='height:80px;'"),
                'output_type' => 'output/longtext',    
            ),
            'difference_reason' => array(
                'label' => __('fcs:narrative:difference_reason'),
                'input_type' => 'input/longtext',
                'input_args' => array('js' => "style='height:80px;'"),
                'output_type' => 'output/longtext',    
            ),
            'resources_used' => array(
                'label' => __('fcs:narrative:resources_used'),
                'help' => __('fcs:narrative:resources_used:help'),                
                'input_type' => 'input/longtext',
                'input_args' => array('js' => "style='height:80px;'"),
                'output_type' => 'output/longtext',    
            ),    
            'intended_results' => array(
                'label' => __('fcs:narrative:intended_results'),
                'input_type' => 'input/longtext',
                'input_args' => array('js' => "style='height:80px;'"),
                'output_type' => 'output/longtext',    
            ),            
            'actual_outcomes' => array(
                'label' => __('fcs:narrative:actual_outcomes'),
                'input_type' => 'input/longtext',
                'input_args' => array('js' => "style='height:80px;'"),
                'output_type' => 'output/longtext',    
            ),
            'other_outcomes' => array(
                'label' => __('fcs:narrative:other_outcomes'),
                'input_type' => 'input/longtext',
                'input_args' => array('js' => "style='height:80px;'"),
                'output_type' => 'output/longtext',    
            ),            
            'outcome_difference_reason' => array(
                'label' => __('fcs:narrative:outcome_difference_reason'),
                'input_type' => 'input/longtext',
                'input_args' => array('js' => "style='height:80px;'"),
                'output_type' => 'output/longtext',    
            ),         
            'lessons_learned' => array(
                'label' => __('fcs:narrative:lessons_learned'),
                'input_type' => 'input/grid',
                'output_type' => 'output/grid',
                'input_args' => array(
                    'row_height' => 80,
                    'initial_rows' => 6,
                    'enable_add_row' => false,
                    'show_row_num' => true,
                ),
                'view_args' => array(
                    'columns' => array(                  
                        'lesson' => array(
                            'label' => __('fcs:narrative:explanation'),
                            'multiline' => true,
                            'width' => 500,
                        ),           
                    )
                )                  
            ),
            'challenges_encountered' => array(
                'label' => __('fcs:narrative:challenges_encountered'),
                'input_type' => 'input/grid',
                'output_type' => 'output/grid',
                'input_args' => array(
                    'row_height' => 80,
                    'initial_rows' => 9,
                    'enable_add_row' => false,
                    'show_row_num' => true,
                ),
                'view_args' => array(
                    'columns' => array(                  
                        'challenge' => array(
                            'label' => __('fcs:narrative:challenge'),
                            'multiline' => true,
                            'width' => 350,
                        ),           
                        'how_overcome' => array(
                            'label' => __('fcs:narrative:how_overcome'),
                            'multiline' => true,
                            'width' => 350,
                        ),                                   
                    )
                )                  
            ),    
            'linkages' => array(
                'label' => __('fcs:narrative:linkages:label'),
                'help' => __('fcs:narrative:linkages:help'),
                'input_type' => 'input/grid',
                'output_type' => 'output/grid',
                'input_args' => array(
                    'row_height' => 80,
                    'initial_rows' => 5,
                    'enable_add_row' => false,
                    'show_row_num' => true,
                ),
                'view_args' => array(
                    'columns' => array(  
                        'organization' => array(
                            'label' => __('fcs:narrative:linkages:organization'),
                            'multiline' => true,
                            'width' => 200,
                        ),                               
                        'description' => array(
                            'label' => __('fcs:narrative:linkages:description'),
                            'multiline' => true,
                            'width' => 400,
                        ),           
                    )
                )                  
            ),            
            'future_activities' => array(
                'label' => __('fcs:narrative:future_activities'),
                'help' => __('fcs:narrative:future_activities:help'),
                'input_type' => 'input/grid',
                'output_type' => 'output/grid',
                'input_args' => array(
                    'row_height' => 80,
                    'initial_rows' => 5,
                    'show_row_num' => true,
                ),                
                'view_args' => array(
                    'columns' => array(
                        'activity' => array(
                            'label' => __('fcs:narrative:future_activities:header'),
                            'multiline' => true,
                            'width' => 400,
                        ),                
                        'month1' => array(
                            'label' => __('fcs:narrative:future_activities:month1'),
                            'width' => 50,
                        ),                
                        'month2' => array(
                            'label' => __('fcs:narrative:future_activities:month2'),
                            'width' => 50,
                        ),    
                        'month3' => array(
                            'label' => __('fcs:narrative:future_activities:month3'),
                            'width' => 50,
                        ),                                                                
                    )
                )
            ),
            'events_attended' => array(
                'label' => __('fcs:narrative:events_attended:label'),
                'help' => __('fcs:narrative:events_attended:help'),
                'input_type' => 'input/grid',
                'output_type' => 'output/grid',
                'input_args' => array(
                    'row_height' => 80,
                ),
                'view_args' => array(
                    'columns' => array(  
                        'event_type' => array(
                            'label' => __('fcs:narrative:events_attended:event_type'),
                            'multiline' => true,
                            'width' => 200,
                        ),                                           
                        'when' => array(
                            'label' => __('fcs:narrative:events_attended:when'),
                            'multiline' => true,
                            'width' => 200,
                        ),   
                        'lessons' => array(
                            'label' => __('fcs:narrative:events_attended:lessons'),
                            'multiline' => true,
                            'width' => 200,
                        ),                               
                        'actions' => array(
                            'label' => __('fcs:narrative:events_attended:actions'),
                            'multiline' => true,
                            'width' => 200,
                        ),           
                    )
                )                  
            ),                        
        );
        
        $beneficiaries_args = array('input_args' => array('js' => 'style="width:80px"'));                
        foreach (array('direct','indirect') as $directness)
        {
            $fields["beneficiaries_male_$directness"] = $beneficiaries_args;
            $fields["beneficiaries_female_$directness"] = $beneficiaries_args;
            $fields["beneficiaries_$directness"] = $beneficiaries_args;
        }
                
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