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
    const Added = 'added';

    // publish_status values
    const Draft = 0;
    const Published = 1;

    static $auto_update_time_updated = false;
    
    static $table_name = 'widgets';
    static $table_base_class = 'Widget';
    static $query_class = 'Query_SelectWidget';
    static $table_attributes = array(
        'subtype_id' => '',
        'local_id' => 0,
        'user_guid' => null,
        'widget_name' => 0,
        'publish_status' => 1,
        'time_published' => null,
        'menu_order' => 0,
        'in_menu' => 1,
        'handler_arg' => '',
        'title' => '',       
        'num_comments' => 0,
        'feed_guid' => null, // optional guid of ExternalFeed object this widget was imported from
    );
    static $mixin_classes = array(
        'Mixin_Content',
        'Mixin_WidgetContainer',
    );
    
    static $default_menu_order = 1;
    static $default_widget_name = '';    
    
    static $default_classes = array(
        'page' => array(
            'Widget_Home',
            'Widget_News',
            'Widget_Projects',
            'Widget_History',
            'Widget_Team',
            'Widget_Volunteer',
            'Widget_Contact',
        ),
        'hidden_page' => array(            
        ),
        'home_section' => array(
            //'Widget_Mission',  
            'Widget_Updates',
            'Widget_Links',
            'Widget_Sectors',
            'Widget_Location',
        ),        
    );

    static function add_default_class($cls, $category = 'page')
    {
        static::$default_classes[$category][] = $cls;
    }
    
    static function get_default_classes($category)
    {
        if (isset(static::$default_classes[$category]))
        {
            return static::$default_classes[$category];
        }
        else
        {
            return array();
        }
    }
    
    static function get_default_class_for_name($widget_name, $category = 'page')
    {
        foreach (static::$default_classes[$category] as $cls)
        {
            if ($cls::$default_widget_name == $widget_name)
            {
                return $cls;
            }
        }
        return null;
    }    
    
    static function get_image_sizes()
    {
        return array(
            'small' => '150x150',
            'medium' => '260x260',
            'large' => '540x980',
        );
    }
        
    public function get_menu_order()
    {
        return $this->menu_order ?: 1000;
    }

    public function query_comments()
    {
        return Comment::query()->where('container_guid = ?', $this->guid)->order_by('tid');
    }

    public function get_title()
    {
        if ($this->title)
        {
            return $this->render_property('title');
        }
        else
        {
            return $this->get_default_title();
        }
    }    
    
    function get_default_title()
    {        
        return  '';
    }
    
    function render_view($args = null)
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
        
        if ($this->is_page() && Widget::get_default_class_for_name($name) == get_class($this))
        {
            return "{$container->get_url()}/{$name}";
        }
        else if ($this->is_section())
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
            $org = $container->get_container_user();
            if ($this->guid)
            {
                return "{$org->get_url()}/node/{$this->get_url_slug()}";
            }
            else
            {
                return "{$org->get_url()}/node/{$this->container_guid}.{$this->widget_name}";
            }
        }
    }    
    
    function get_edit_url()
    {
        return "{$this->get_base_url()}/edit";
    }
            
    public function is_section()
    {
        return $this->get_container_entity()->is_section_container();
    }
        
    public function is_page()
    {    
        return $this->get_container_entity()->is_page_container();
    }

    function get_breadcrumb_items()
    {
        $args = array();
        for ($cur = $this, $i = 0; 
            $cur instanceof Widget && $i < 10; 
            $cur = $cur->get_container_entity(), $i++)
        {
            $args[] = array('url' => $cur->get_url(), 'title' => $cur->get_title());
        }
        return array_reverse($args);
    }    
    
    function post_feed_items()
    {
        return FeedItem_NewWidget::post($this->get_container_user(), $this);
    }
    
    function post_feed_items_edit()    
    {
        return FeedItem_EditWidget::post($this->get_container_user(), $this); 
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

    function get_view_types()
    {
        return array();
    }    
    
    function save()
    {
        if ($this->publish_status == Widget::Published && !$this->time_published)
        {
            $this->time_published = timestamp();
        }    
        
        if (!$this->owner_guid)
        {
            $this->set_owner_entity(Session::get_logged_in_user());
        }

        if (!$this->user_guid)
        {
            $this->user_guid = $this->get_container_user()->guid;
        }

        if (!$this->local_id && !$this->is_page())
        {
            $this->generate_local_id();
        }
        
        parent::save();
    }
    
    public function get_date_text($time = null)
    {
        return friendly_time($time ?: $this->time_published);
    }    
    
    private $url_slug = null;
    
    public function get_url_slug()
    {
        if (!$this->url_slug)
        {
            $tid = $this->local_id;
            $title = $this->title;            
            
            $this->url_slug = $tid;

            if ($title && $tid)
            {
                $title = static::make_url_slug($title);

                if ($title)
                {
                    $this->url_slug = "{$title},{$tid}";
                }
            }
        }
        return $this->url_slug;       
    }    
    
    static function make_url_slug($title)
    {                
        // adapted from https://gist.github.com/853906#file_gistfile2.php
        $title = strtolower(substr($title, 0, 64));
        $title = trim(preg_replace("/[^a-z0-9\s-]/", "", $title));
        $title = preg_replace("/[\s-]+/", "-", $title);
        return $title;
    }
    
    function save_draft($content)
    {
        if (!$this->guid || $this->status == Entity::Disabled)        
        {
            $this->publish_status = Widget::Draft;
            $this->enable();            
            $this->save();            
        }
        parent::save_draft($content);            
    }
    
    function refresh_attributes()
    {
		$this->num_comments = $this->query_comments()->count();
    }    
    
    static function get_view_permission()
    {
        return 'Permission_ViewUserSite';    
    }
    
    static function query_for_entity($entity)
    {
        return static::query()
            ->where('container_guid=?', $entity->guid)
            ->order_by('menu_order');
    }
        
    static function new_for_entity($entity, $props = null)        
    {
        $cls = get_called_class();
        
        $widget = new $cls();
        $widget->set_container_entity($entity);
        $widget->menu_order = static::$default_menu_order;
        $widget->widget_name = static::$default_widget_name;
        if ($props)
        {
            foreach ($props as $k => $v)
            {
                $widget->$k = $v;
            }
        }        
        return $widget;
    }
        
    static function init_for_entity($entity, $props = null)
    {
        $widget = static::new_for_entity($entity, $props);
        $widget->save();
        return $widget;
    }
    
    static function get_for_entity($entity)
    {
        return static::query_for_entity($entity)
            ->show_disabled(true)
            ->order_by('status desc') // prefer enabled over disabled widgets
            ->get();        
    }

    static function get_or_init_for_entity($entity, $defaults = null)
    {    
        return static::get_for_entity($entity) ?: static::init_for_entity($entity, $defaults);
    }
    
    static function get_or_new_for_entity($entity, $defaults = null)
    {
        return static::get_for_entity($entity) ?: static::new_for_entity($entity, $defaults);
    }
    
    function execute_custom_action($controller)
    {
        $controller->use_public_layout();
        throw new NotFoundException();
    }
    
    function generate_local_id()
    {
        $guid = $this->get_guid();
          
        $user = $this->get_container_user();        
        $user_guid = $user->guid;        
          
        $max_row = Database::get_row("SELECT max(local_id) as max FROM ".static::$table_name." where user_guid = ?", array($user_guid));
        
        $max_id = $max_row ? ((int)$max_row->max) : 0;
        
        for ($i = 1; $i < 10; $i++)
        {
            try
            {
                $local_id = $max_id + $i;
            
                Database::update("INSERT INTO local_ids (guid, user_guid, local_id) VALUES (?,?,?)",
                    array($guid, $user_guid, $local_id));
                    
                $this->local_id = $local_id;
                
                return;
            }
            catch (DatabaseException $ex)
            {
                // duplicate local_id? try next one
            }
        }
    }        
}
