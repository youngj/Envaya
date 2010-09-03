<?php

class Widget extends Entity
{
    static $subtype_id = T_widget;
    static $table_name = 'widgets';
    static $table_attributes = array(
        'widget_name' => 0,
        'menu_order' => 0,
        'in_menu' => 1,
        'handler_class' => '',
        'handler_arg' => '',
        'title' => '',
        'content' => '',
        'data_types' => 0,
        'language' => '',
    );
    
    static $default_widgets = array(
        'home'          => array('menu_order' => 10, 'handler_class' => 'WidgetHandler_Home'),
        'news'          => array('menu_order' => 20, 'handler_class' => 'WidgetHandler_News'),
        'projects'      => array('menu_order' => 30, 'handler_class' => 'WidgetHandler_Generic'),
        'history'       => array('menu_order' => 40, 'handler_class' => 'WidgetHandler_Generic'),
        'team'          => array('menu_order' => 50, 'handler_class' => 'WidgetHandler_Team'),
        'partnerships'  => array('menu_order' => 60, 'handler_class' => 'WidgetHandler_Partnerships'),
        'reports'       => array('menu_order' => 65, 'handler_class' => 'WidgetHandler_Reports'),
        'contact'       => array('menu_order' => 70, 'handler_class' => 'WidgetHandler_Contact'),        
    );    
    
    static function get_default_names()
    {
        return array_keys(static::$default_widgets);
    }
    
    static function get_image_sizes()
    {
        return array(
            'small' => '150x150',
            'medium' => '260x260',
            'large' => '520x520',
        );
    }    
        
    public function get_menu_order()
    {
        return $this->menu_order ?: @static::$default_widgets[$this->widget_name]['menu_order'] ?: 100;
    }

    public function get_title()
    {
        if ($this->title)
        {
            return $this->translate_field('title', false);
        }
        else
        {
            $key = "widget:{$this->widget_name}";
            $title = __($key);
            return ($title != $key) ? $title : __('widget:new');
        }
    }    
    
    function get_handler_class()
    {
        $handlerCls = $this->handler_class;
        if (!$handlerCls)
        {
            $handlerCls = @static::$default_widgets[$this->widget_name]['handler_class'] ?: 'WidgetHandler_Generic';
        }
        return $handlerCls;
    }    
    
    function get_handler()
    {
        try
        {
            $handlerCls = new ReflectionClass($this->get_handler_class());
            
            if ($this->handler_arg)
            {
                return $handlerCls->newInstance($this->handler_arg);
            }
            else
            {
                return $handlerCls->newInstance();            
            }            
        }
        catch (ReflectionException $ex)
        {        
            return new WidgetHandler_Invalid();
        }        
    }
    
    function render_view()
    {
        return $this->get_handler()->view($this);
    }

    function render_edit()
    {
        return $this->get_handler()->edit($this);
    }

    function save_input()
    {
        return $this->get_handler()->save($this);
    }
    
    public function has_image()
    {
        return ($this->data_types & DataType::Image) != 0;
    }    
        
    function get_url()
    {
        $org = $this->get_container_entity();

        if ($this->widget_name == 'home')
        {
            return $org->get_url();
        }
        else
        {
            return "{$org->get_url()}/{$this->widget_name}";
        }
    }

    function get_base_url()
    {
        $org = $this->get_container_entity();
        return "{$org->get_url()}/{$this->widget_name}";
    }    
    
    function get_edit_url()
    {
        return "{$this->get_base_url()}/edit";
    }
            
    public function is_active()
    {
        return $this->guid && $this->is_enabled();
    }
    
    static function sort($a, $b)
    {
        $aOrder = $a->get_menu_order();
        $bOrder = $b->get_menu_order();
        return $aOrder - $bOrder;
    }
}

