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
                'field_names' => array(
                    'full_name',
                    'other_name',
                    'project_name',
                    'reference_num',
                    'report_period',
                    'project_coordinator'
                )
            ),
            2 => array(
                'title' => __('fcs:narrative:description'),
                'field_names' => array(
                    'thematic_areas',
                    'project_description',
                    'regions',
                    'total_beneficiaries'
                )                    
            ),
            3 => array(
                'title' => __('fcs:narrative:activities'),
                'field_names' => array(
                    'outputs',
                    'planned_activities',
                    'achievements',
                    'difference_reason',
                    'resources_used'                
                )
            ),
            4 => array(
                'title' => __('fcs:narrative:outcomes'),
                'field_names' => array(
                    'intended_results',
                    'actual_outcomes',
                    'other_outcomes',
                    'outcome_difference_reason'
                ),
            ), 
            5 => array(
                'title' => __('fcs:narrative:lessons'),
                'field_names' => array('lessons_learned'),
            ),
            6 => array(
                'title' => __('fcs:narrative:challenges'),
                'field_names' => array('challenges_encountered'),
            ),         
            7 => array(
                'title' => __('fcs:narrative:linkages'),
                'field_names' => array('linkages'),
            ),
            8 => array(
                'title' => __('fcs:narrative:future_plans'),
                'field_names' => array('future_activities'),
            ), 
            9 => array(
                'title' => __('fcs:narrative:beneficiaries'),
                'field_names' => array('beneficiaries_container','beneficiaries_other_details'),
            ),
            10 => array(
                'title' => __('fcs:narrative:events_attended'),
                'field_names' => array('events_attended'),
            ),        
            11 => array(
                'title' => __('fcs:narrative:attachments'),
                'field_names' => array('success_story_1','success_story_2','success_story_3'),
            )
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
                'custom_view' => 'fcs/narrative/report_period'
            ),
            'report_dates' => array(
                'label' => __('fcs:narrative:report_dates'),
                'input_args' => array('js' => "style='width:250px;'"),
            ),
            'report_quarters' => array(
                'label' => __('fcs:narrative:report_quarters'),
                'input_args' => array('js' => "style='width:100px;'"),
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
                            'editor' => 'DropDownListCellEditor',
                            'formatter' => 'DropDownListCellFormatter',
                            'args' => array(
                                'options' => regions_in_country('tz'),
                            ),
                            'width' => 200,
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
            'total_beneficiaries' => array(
                'label' => __('fcs:narrative:total_beneficiaries'),
                'help' => __('fcs:narrative:beneficiaries_container:help'),
                'custom_view' => 'fcs/narrative/total_beneficiaries'
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
            'beneficiaries_container' => array(                
                'label' => __('fcs:narrative:beneficiaries_container'),
                'help' => __('fcs:narrative:beneficiaries_container:help'),
                'custom_view' => 'fcs/narrative/activity_beneficiaries',
                'view_args' => array(
                    //'num_activities' => 3,
                    'constituencies' => array('widows','hiv_aids','elderly','orphans','children','disabled','youth','other')
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
                            'editor' => 'DateCellEditor'
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
            'success_story_1' => array(
                'label' => __('fcs:narrative:success_story:label'),
                'help' => __('fcs:narrative:success_story:help'),
                'input_type' => 'input/upload',
                'output_type' => 'output/upload',                
            ),
            'success_story_2' => array(
                'input_type' => 'input/upload',
                'output_type' => 'output/upload',                
            ),
            'success_story_3' => array(
                'input_type' => 'input/upload',
                'output_type' => 'output/upload',                
            ),

        );
        
        $directnesses = array('direct','indirect');
        foreach ($directnesses as $directness)
        {
            $male_field = "beneficiaries_male_$directness";
            $female_field = "beneficiaries_female_$directness";
            $total_field = "beneficiaries_$directness";
        
            $beneficiaries_args = array(
                'input_args' => array('js' => "style='width:80px'"),
                'auto_update' => $total_field,
            );

            $fields[$male_field] = $beneficiaries_args;
            $fields[$female_field] = $beneficiaries_args;
            
            $total_args = array(
                'input_args' => array('js' => "style='width:80px'"),
                'auto_value' => "getInteger('$male_field') + getInteger('$female_field')"
            );
            
            $fields[$total_field] = $total_args;
        }
                        
        //for ($activity_num = 1; $activity_num <= $fields['beneficiaries_container']['view_args']['num_activities']; $activity_num++)
        //{                  
            foreach ($fields['beneficiaries_container']['view_args']['constituencies'] as $constituency)
            {
                foreach ($directnesses as $directness)
                {
                    $total_field = $constituency."_".$directness;
                    $female_field = $constituency."_female_".$directness;
                    $male_field = $constituency."_male_".$directness;
                    
                    $constituency_args = array('input_args' => 
                        array(
                            'class' => ' ', 
                            'js' => "style='width:100px; font-size:120%'",
                        ));
                        
                    $gender_args = $constituency_args;
                    $gender_args['auto_update'] = $total_field;

                    $total_args = $constituency_args;
                    $total_args['auto_value'] = "getInteger('$male_field') + getInteger('$female_field')";                    
                
                    $fields[$female_field] = $gender_args;
                    $fields[$male_field] = $gender_args;
                    $fields[$total_field] = $total_args;
                }
            }
        //}
        
        $fields['beneficiaries_other_details'] = array(
            'label' => __('fcs:narrative:beneficiaries_other_details')
        );
        
        return $fields;        
    }    
}