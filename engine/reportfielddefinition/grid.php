<?php

class ReportFieldDefinition_Grid extends ReportFieldDefinition
{
    public $columns;

    function __construct($args)
    {
        $args['input_type'] = 'input/grid';
        $args['output_type'] = 'output/grid';
        
        parent::__construct($args);        
    }
    
    function get_exported_values($field)
    {
        $res = array();
        $grid_rows = json_decode($field->value, true);
        
        if (is_array($grid_rows))
        {
            for ($i = 0; $i < sizeof($grid_rows); $i++)
            {
                $grid_row = $grid_rows[$i];
                $row_num = $i + 1;
                
                foreach ($grid_row as $column_name => $value)
                {                
                    $column_def = @$this->columns[$column_name];
                    
                    if ($column_def)
                    {                
                        $res["{$field->name}_{$column_name}_{$row_num}"] = static::render_cell_value($value, $column_def);
                    }
                }
            }
        }    
        
        return $res;
    }

    static function render_cell_value($value, $column_def)
    {
        $args = @$column_def['args'] ?: array();
        $output_args = @$column_def['output_args'];
        if ($output_args)
        {
            foreach ($output_args as $k => $v)
            {
                $args[$k] = $v;
            }   
        }
        $args['value'] = $value;    
                
        return view((@$column_def['output_type'] ?: 'output/text'), $args);       
    }
    
    protected function get_input_args($field)
    {
        $args = parent::get_input_args($field);
        $args['columns'] = $this->columns;
        return $args;
    }
    
    protected function get_output_args($field)
    {
        $args = parent::get_output_args($field);
        $args['columns'] = $this->columns;
        return $args;
    }       
        
}