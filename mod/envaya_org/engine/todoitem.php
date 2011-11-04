<?php

/*
 * Some action that we want organizations to do.
 */
class TodoItem
{
    public $link;
    public $done;
    public $score = 1;
    
    public function __construct($link, $done, $score = 1)
    {
        $this->link = $link;
        $this->done = $done;
        $this->score = $score;
    }
    
    static function get_total_score($org)
    {
        $score = 0;
        foreach (static::get_list($org) as $item)
        {
            if ($item->done)
            {
                $score += $item->score;
            }
        }
        return $score;
    }
    
    static function get_list($org)
    {
        $items = array();
        
        $widget_classes = array(
            'Widget_Home',
            'Widget_Contact',
            'Widget_History',
            'Widget_Projects',
            'Widget_Team',
            'Widget_News',
            'Widget_Network',
            'Widget_Discussions',
        );
        
        $query = $org->query_widgets()
            ->where_in('subtype_id', array_map(function($cls) { return $cls::get_subtype_id(); }, $widget_classes))
            ->columns('guid, status, time_created, time_updated, container_guid, owner_guid,
                        widget_name, subtype_id, title, length(content) as content_len');
        
        $widgets = $query->filter();
        
        $widgets_map = array();
        foreach ($widgets as $widget)
        {
            $widgets_map[get_class($widget)] = $widget;
        }
        foreach ($widget_classes as $widget_class)
        {    
            if (!isset($widgets_map[$widget_class]))
            {
                $widgets_map[$widget_class] = $widget_class::new_for_entity($org);
            }
        }
            
        $recent_time = timestamp() - 86400 * 31;
    
        $home = $widgets_map['Widget_Home'];
        $items[] = new TodoItem(
            "<a href='{$home->get_edit_url()}'>".__('todo:home')."</a>", 
            true
        );
        
        $contact = $widgets_map['Widget_Contact'];
        $items[] = new TodoItem(
            "<a href='{$contact->get_edit_url()}'>".__('todo:contact')."</a>", 
            $contact->time_updated > $contact->time_created
        );

        $history = $widgets_map['Widget_History'];
        $items[] = new TodoItem(
            "<a href='{$history->get_edit_url()}'>".__('todo:history')."</a>",
            $history->is_enabled() && $history->content_len > 0
        );            

        $projects = $widgets_map['Widget_Projects'];
        $items[] = new TodoItem(
            "<a href='{$projects->get_edit_url()}'>".__('todo:projects')."</a>",
            $projects->is_enabled() && $projects->content_len > 0
        );            
        
        $team = $widgets_map['Widget_Team'];
        $items[] = new TodoItem(
            "<a href='{$team->get_edit_url()}'>".__('todo:team')."</a>",
            $team->is_enabled() && $team->content_len > 0
        );            
        
        $news = $widgets_map['Widget_News'];
        $hasRecentNews = $news->query_widgets()->where('time_created > ?', $recent_time)->exists();
        $items[] = new TodoItem(
            "<a href='{$news->get_edit_url()}'>".__('todo:news')."</a>",
            $hasRecentNews
        );            
        
        $numImages = $org->query_files()->where("size='small'")->where('time_created > ?', $recent_time)->count();
        $items[] = new TodoItem(
            "<a href='{$org->get_url()}/addphotos'>".__('todo:photos')."</a>",
            $numImages >= 2
        );        
               
        $items[] = new TodoItem(
            "<a href='{$org->get_url()}/design'>".__('todo:logo')."</a>",
            ($org->get_design_setting('header_image') || $org->has_custom_icon())    
        );

        $network = $widgets_map['Widget_Network'];
        $items[] = new TodoItem(
            "<a href='{$network->get_edit_url()}'>".__('todo:network')."</a>",
            $network->is_enabled()
        );
        
        $discussions = $widgets_map['Widget_Discussions'];
        $items[] = new TodoItem(
            "<a href='{$discussions->get_edit_url()}'>".__('todo:discussions')."</a>",
            $discussions->is_enabled()
        );        
        
        return $items;
    }
}