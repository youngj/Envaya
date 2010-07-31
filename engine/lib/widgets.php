<?php

class Widget extends ElggObject
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
    
    static $defaultWidgets = array(
        'home'          => array('menu_order' => 10, 'handler_class' => 'WidgetHandler_Home'),
        'news'          => array('menu_order' => 20, 'handler_class' => 'WidgetHandler_News'),
        'projects'      => array('menu_order' => 30, 'handler_class' => 'WidgetHandler_Generic'),
        'history'       => array('menu_order' => 40, 'handler_class' => 'WidgetHandler_Generic'),
        'team'          => array('menu_order' => 50, 'handler_class' => 'WidgetHandler_Team'),
        'partnerships'  => array('menu_order' => 60, 'handler_class' => 'WidgetHandler_Partnerships'),
        'contact'       => array('menu_order' => 70, 'handler_class' => 'WidgetHandler_Contact'),
    );    
    
    static function getDefaultNames()
    {
        return array_keys(static::$defaultWidgets);
    }
    
    static function getImageSizes()
    {
        return array(
            'small' => '150x150',
            'medium' => '260x260',
            'large' => '520x520',
        );
    }    
        
    public function getMenuOrder()
    {
        return $this->menu_order ?: @static::$defaultWidgets[$this->widget_name]['menu_order'] ?: 100;
    }

    public function getTitle()
    {
        if ($this->title)
        {
            return translate_field($this, 'title', false);
        }
        else
        {
            $key = "widget:{$this->widget_name}";
            $title = __($key);
            return ($title != $key) ? $title : __('widget:new');
        }
    }    
    
    function getHandlerClass()
    {
        $handlerCls = $this->handler_class;
        if (!$handlerCls)
        {
            $handlerCls = @static::$defaultWidgets[$this->widget_name]['handler_class'] ?: 'WidgetHandler_Generic';
        }
        return $handlerCls;
    }    
    
    function getHandler()
    {
        try
        {
            $handlerCls = new ReflectionClass($this->getHandlerClass());
            
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
    
    function renderView()
    {
        return $this->getHandler()->view($this);
    }

    function renderEdit()
    {
        return $this->getHandler()->edit($this);
    }

    function saveInput()
    {
        return $this->getHandler()->save($this);
    }
    
    public function hasImage()
    {
        return ($this->data_types & DataType::Image) != 0;
    }    
        
    function getURL()
    {
        $org = $this->getContainerEntity();

        if ($this->widget_name == 'home')
        {
            return $org->getURL();
        }
        else
        {
            return "{$org->getURL()}/{$this->widget_name}";
        }
    }

    function getBaseURL()
    {
        $org = $this->getContainerEntity();
        return "{$org->getURL()}/{$this->widget_name}";
    }    
    
    function getEditURL()
    {
        return "{$this->getBaseURL()}/edit";
    }
    
    public function allowUnsafeHTML()
    {
        $container = $this->getContainerEntity();
        return ($container && $container->allowUnsafeHTML());
    }
        
    public function isActive()
    {
        return $this->guid && $this->isEnabled();
    }
}

function widget_sort($a, $b)
{
    $aOrder = $a->getMenuOrder();
    $bOrder = $b->getMenuOrder();
    return $aOrder - $bOrder;
}

abstract class WidgetHandler
{
    abstract function view($widget);
    abstract function edit($widget);
    abstract function save($widget);
}

