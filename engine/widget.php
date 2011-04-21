<?php

/*
 * Represents a 'page' on an organization's website,
 * like /<username>/page/foo or /<username>/contact ,
 * and provides methods for viewing and editing that page.
 *
 * However, some widgets have more complex behavior
 * than just a standard content page (e.g. a news feed).
 * The behavior of a widget is determined by its corresponding 
 * subclass (a subclass of Widget with class name Widget_{subclass}).
 */
class Widget extends Entity
{
    static $table_name = 'widgets';
    static $table_attributes = array(
        'widget_name' => 0,
        'menu_order' => 0,
        'in_menu' => 1,
        'subclass' => '',
        'handler_arg' => '',
        'title' => '',       
    );
    static $mixin_classes = array(
        'Mixin_Content',
        'Mixin_WidgetContainer',
    );
    
    static $default_widgets = array(
        'home'          => array('menu_order' => 10, 'page' => true, 'subclass' => 'Home'),
        'news'          => array('menu_order' => 20, 'page' => true, 'subclass' => 'News'),
        'projects'      => array('menu_order' => 30, 'page' => true, 'subclass' => 'Generic'),
        'history'       => array('menu_order' => 40, 'page' => true, 'subclass' => 'Generic'),
        'team'          => array('menu_order' => 50, 'page' => true, 'subclass' => 'Team'),
        'network'       => array('menu_order' => 60, 'page' => true, 'subclass' => 'Network'),
        'discussions'   => array('menu_order' => 70, 'page' => true, 'subclass' => 'Discussions'),
        'contact'       => array('menu_order' => 90, 'page' => true, 'subclass' => 'Contact'),
        'mission'       => array('menu_order' => 100, 'home_section' => true, 'subclass' => 'Mission'),        
        'updates'       => array('menu_order' => 110, 'home_section' => true, 'subclass' => 'Updates'),        
        'sectors'       => array('menu_order' => 120, 'home_section' => true, 'subclass' => 'Sectors'),        
        'location'      => array('menu_order' => 130, 'home_section' => true, 'subclass' => 'Location'),        
    );
    
    static function get_subtype_id()
    {
        // all subclasses share same subtype id
        return EntityRegistry::get_subtype_id('Widget'); 
    }    
    
    static function add_default_widget($widget_name, $props)
    {
        static::$default_widgets[$widget_name] = $props;
    }
    
    static function get_default_names_by_class($subclass)
    {
        $names = array();
        foreach (static::$default_widgets as $widget_name => $args)
        {
            if (@$args['subclass'] == $subclass)
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
        return $this->menu_order ?: 1000;
    }

    public function query_comments()
    {
        return Comment::query()->where('container_guid = ?', $this->guid)->order_by('guid');
    }

    public function get_subtitle()
    {
        if ($this->title)
        {
            return $this->translate_field('title', false);
        }
        else
        {
            return $this->get_default_subtitle();
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
            return $this->get_default_title();
        }
    }    
    
    function get_default_title()
    {
        $key = "widget:{$this->widget_name}";
        $title = __($key);
        return ($title != $key) ? $title : __('widget:new');
    }
    
    function get_default_subtitle()
    {
        return $this->get_default_title();
    }
        
    static function new_from_row($row)
    {
        $cls = "Widget_{$row->subclass}";        
        if (class_exists($cls))
        {
            return new $cls($row);
        }
        else
        {
            return new Widget_Invalid($row);
        }        
    }
    
    function render_view()
    {
        return '';
    }

    function render_view_feed()
    {
        return '';
    }
    
    function render_edit()
    {
        return '';
    }

    function process_input($action)
    {
        
    }
    
    function get_url()
    {
        $name = $this->widget_name;
        $container = $this->get_container_entity();
        $is_page = $this->is_page();
        
        if ($is_page && isset(static::$default_widgets[$name]))
        {
            return "{$container->get_url()}/{$name}";
        }
        else if (!$is_page)
        {
            return $container->get_url();
        }        
        else
        {
            return $this->get_base_url();
        }
    }

    function get_base_url()
    {        
        $container = $this->get_container_entity();        
        if (!$container)
        {
            return null;
        }
        
        if ($this->is_page())
        {
            return "{$container->get_url()}/page/{$this->widget_name}";
        }
        else
        {
            $org = $container->get_root_container_entity();
            if ($this->guid)
            {
                return "{$org->get_url()}/widget/{$this->guid}";
            }
            else
            {
                return "{$org->get_url()}/widget/{$this->container_guid}_{$this->widget_name}";
            }
        }
    }    
    
    function get_edit_url()
    {
        return "{$this->get_base_url()}/edit";
    }
            
    public function is_active()
    {
        return $this->guid && $this->is_enabled();
    }
    
    public function is_page()
    {
        return ($this->get_container_entity() instanceof Organization);
    }
    
    function post_feed_items_new()
    {
        return FeedItem_NewWidget::post($this->get_root_container_entity(), $this);
    }
    
    function post_feed_items()    
    {
        return FeedItem_EditWidget::post($this->get_root_container_entity(), $this); 
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
    
}
