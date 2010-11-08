<?php

class ReportField extends Model
{
    static $table_name = 'report_fields';
    static $table_attributes = array(
        'name' => '',
        'value' => '',
        'value_type' => 0,
        'report_guid' => 0
    );   
    
    function get($name)
    {
        $value = parent::get($name);

        if ($name == 'value')
        {
            return VariantType::decode_value($value, $this->attributes['value_type']);
        }
        return $value;
    }

    function set($name, $value)
    {
        if ($name == 'value')
        {           
            $value = VariantType::encode_value($value, $this->attributes['value_type']);            
        }
        parent::set($name, $value);
    }         
    
    function get_option_text($value)
    {
        $options = $this->get_arg('options');
        if ($options)
        {
            return @$options[$value] ?: $value;
        }
        else
        {
            return $value;
        }
    }
    
    function get_csv_value()
    {   
        $value = $this->value;
        if (is_array($value))
        {
            return implode(",", array_map(array($this, 'get_option_text'), $value));
        }
        else
        {
            return $this->get_option_text($value);
        }
    }

    function get_args()
    {
        $report = $this->get_report();
        $args = $report->get_field_args();
        return @$args[$this->name];
    }
    
    function get_arg($arg_name)
    {
        $args = $this->get_args();
        return @$args[$arg_name];
    }
    
    function label()
    {
        return $this->get_arg('label');
    }

    function help()
    {
        return $this->get_arg('help');
    }
    
    function get_report()
    {
        return get_entity($this->report_guid);
    }
    
    function is_blank()
    {
        $value = $this->value;
        return !$value && $value !== 0 && $value !== '0';
    }
    
    function view_html()
    {        
        return view('reports/view_field', array('field' => $this));
    }

    function edit_html()
    {        
        return view('reports/edit_field', array('field' => $this));
    }
    
    private function render_custom_view($view_name, $edit = false)
    {
        return view("reports/$view_name", array('field' => $this, 'report' => $this->get_report(), 'edit' => $edit));    
    }
    
    function view_input()
    {   
        $args = $this->get_args();
        $custom_view = @$args['custom_view'];
        if ($custom_view)
        {
            return $this->render_custom_view($custom_view);
        }           
        
        if (!$this->is_blank())
        {    
            $output_args = array('value' => $this->value);
            
            foreach (array('view_args','output_args') as $arg_group)
            {
                if (isset($args[$arg_group]))
                {
                    foreach ($args[$arg_group] as $k => $v)
                    {
                        $output_args[$k] = $v;
                    }
                }
            }
        
            return view(@$args['output_type'] ?: 'output/text', $output_args);
        }
        else
        {
            return "<em>".__('report:blank')."</em>";
        }
    }
    
    function edit_input()
    {   
        $args = $this->get_args();
        
        $custom_view = @$args['custom_view'];
        if ($custom_view)
        {
            return $this->render_custom_view($custom_view, true);
        }                   
        
        $input_name = "field_{$this->name}";
    
        $input_args = array(
            'internalname' => $input_name,
            'trackDirty' => true,
            'value' => !$this->is_blank() ? $this->value : @$args['default'],
        );
        
        foreach (array('view_args','input_args') as $arg_group)
        {
            if (isset($args[$arg_group]))
            {
                foreach ($args[$arg_group] as $k => $v)
                {
                    $input_args[$k] = $v;
                }
            }
        }
    
        $res = view(@$args['input_type'] ?: 'input/text', $input_args);       
               
        $res .= view('input/hidden', array(
            'internalname' => 'fields[]',
            'value' => $this->name
        )); 
        
        return $res;
    }    
}