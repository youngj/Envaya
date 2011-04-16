<?php

/*
 * Represents a 'page' on an organization's website,
 * like /<username>/page/foo or /<username>/contact ,
 * and provides methods for viewing and editing that page.
 *
 * However, some widgets have more complex behavior
 * than just a standard content page (e.g. a news feed).
 * The behavior of a widget is determined by its corresponding 
 * handler_class (a subclass of WidgetHandler).
 */
class Widget extends Entity
{
    static $table_name = 'widgets';
    static $table_attributes = array(
        'widget_name' => 0,
        'menu_order' => 0,
        'in_menu' => 1,
        'handler_class' => '',
        'handler_arg' => '',
        'title' => '',
    );
    static $mixin_classes = array(
        'Mixin_Content'
    );
    
    static $default_widgets = array(
        'home'          => array('menu_order' => 10, 'handler_class' => 'WidgetHandler_Home'),
        'news'          => array('menu_order' => 20, 'handler_class' => 'WidgetHandler_News'),
        'projects'      => array('menu_order' => 30, 'handler_class' => 'WidgetHandler_Generic'),
        'history'       => array('menu_order' => 40, 'handler_class' => 'WidgetHandler_Generic'),
        'team'          => array('menu_order' => 50, 'handler_class' => 'WidgetHandler_Team'),
        'network'       => array('menu_order' => 60, 'handler_class' => 'WidgetHandler_Network'),
        'discussions'   => array('menu_order' => 70, 'handler_class' => 'WidgetHandler_Discussions'),
        'reports'       => array('menu_order' => 80, 'hidden' => true, 'handler_class' => 'WidgetHandler_Reports'),
        'contact'       => array('menu_order' => 90, 'handler_class' => 'WidgetHandler_Contact'),        
    );        
    
    static function get_default_names_by_class($class_name)
    {
        $names = array();
        foreach (static::$default_widgets as $widget_name => $args)
        {
            if (@$args['handler_class'] == $class_name)
            {
                $names[] = $widget_name;
            }
        }
        return $names;
    }
    
    static function get_default_names()
    {
        return array_keys(static::$default_widgets);
    }
    
    static function get_image_sizes()
    {
        return array(
            'small' => '150x150',
            'medium' => '260x260',
            'large' => '540x1080',
        );
    }
        
    public function get_menu_order()
    {
        return $this->menu_order ?: @static::$default_widgets[$this->widget_name]['menu_order'] ?: 100;
    }

    public function query_comments()
    {
        return Comment::query()->where('container_guid = ?', $this->guid)->order_by('e.guid');
    }

    public function get_subtitle()
    {
        if ($this->title)
        {
            return $this->translate_field('title', false);
        }
        else
        {
            return $this->get_handler()->get_default_subtitle($this);
        }    
    }
	
    public function get_title()
    {
        if ($this->title)
        {
            return $this->translate_field('title', false);
        }
        else
        {
            return $this->get_handler()->get_default_title($this);
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
        
        $name = $this->widget_name;

        if (@static::$default_widgets[$name])
        {
            return "{$org->get_url()}/{$name}";
        }
        else
        {
            return "{$org->get_url()}/page/{$name}";
        }
    }

    function get_base_url()
    {
        $org = $this->get_container_entity();
        return "{$org->get_url()}/page/{$this->widget_name}";
    }    
    
    function get_edit_url()
    {
        return "{$this->get_base_url()}/edit";
    }
            
    public function is_active()
    {
        return $this->guid && $this->is_enabled();
    }
    
    function post_feed_items_new()
    {
        return post_feed_items($this->get_container_entity(), 'newwidget', $this);
    }
    
    function post_feed_items()    
    {
        return post_feed_items($this->get_container_entity(), 'editwidget', $this); 
    }
    
    static function is_valid_name($widget_name)
    {
        if (!$widget_name || preg_match('/[^\w\.\-]/', $widget_name))
        {
            return false;            
        }
        return true;
    }
    
    static function sort($a, $b)
    {
        $aOrder = $a->get_menu_order();
        $bOrder = $b->get_menu_order();
        return $aOrder - $bOrder;
    }
    
    static function new_default_widget($widget_name)
    {
        $widget = new Widget();
        $widget->widget_name = $widget_name;    
        
        $props = @static::$default_widgets[$widget_name];
        if ($props)
        {
            $widget->menu_order = $props['menu_order'];
            $widget->handler_class = $props['handler_class'];            
        }
        else
        {
            $widget->menu_order = 1000;
            $widget->hander_class = 'WidgetHandler_Generic';
        }
        return $widget;
    }
}

