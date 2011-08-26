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
        
        $widget_names = array('home', 'contact','history','projects','team','news','network','discussions');
        
        $query = $org->query_widgets()
            ->where_in('widget_name', $widget_names)
            ->columns('guid, status, time_created, time_updated, container_guid, owner_guid,
                        widget_name, subclass, title, length(content) as content_len');
        
        $widgets = $query->filter();
        
        $widgets_map = array();
        foreach ($widgets as $widget)
        {
            $widgets_map[$widget->widget_name] = $widget;
        }
        foreach ($widget_names as $widget_name)
        {    
            if (!isset($widgets_map[$widget_name]))
            {
                $widgets_map[$widget_name] = $org->new_widget_by_name($widget_name);
            }
        }
            
        $recent_time = timestamp() - 86400 * 31;
    
        $home = $widgets_map['home'];
        $items[] = new TodoItem(
            "<a href='{$home->get_edit_url()}'>".__('todo:home')."</a>", 
            true
        );
        
        $contact = $widgets_map['contact'];
        $items[] = new TodoItem(
            "<a href='{$contact->get_edit_url()}'>".__('todo:contact')."</a>", 
            $contact->time_updated > $contact->time_created
        );

        $history = $widgets_map['history'];
        $items[] = new TodoItem(
            "<a href='{$history->get_edit_url()}'>".__('todo:history')."</a>",
            $history->is_enabled() && $history->content_len > 0
        );            

        $projects = $widgets_map['projects'];
        $items[] = new TodoItem(
            "<a href='{$projects->get_edit_url()}'>".__('todo:projects')."</a>",
            $projects->is_enabled() && $projects->content_len > 0
        );            
        
        $team = $widgets_map['team'];
        $items[] = new TodoItem(
            "<a href='{$team->get_edit_url()}'>".__('todo:team')."</a>",
            $team->is_enabled() && $team->content_len > 0
        );            
        
        $news = $widgets_map['news'];
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

        $network = $widgets_map['network'];
        $items[] = new TodoItem(
            "<a href='{$network->get_edit_url()}'>".__('todo:network')."</a>",
            $network->is_enabled()
        );
        
        $discussions = $widgets_map['discussions'];
        $items[] = new TodoItem(
            "<a href='{$discussions->get_edit_url()}'>".__('todo:discussions')."</a>",
            $discussions->is_enabled()
        );        
        
        return $items;
    }
}