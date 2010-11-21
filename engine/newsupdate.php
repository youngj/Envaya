<?php

class NewsUpdate extends Entity
{
    static $subtype_id = T_blog;
    static $table_name = 'news_updates';
    static $table_attributes = array(
        'content' => '',
        'data_types' => 0,
        'language' => '',
		'num_comments' => 0
    );

    public function query_comments()
    {
        return Comment::query()->where('container_guid = ?', $this->guid)->order_by('e.guid');
    }
    
    public function get_title()
    {
        return __("widget:news:item");
    }

    public function js_properties()
    {
        return array(
            'guid' => $this->guid,
            'container_guid' => $this->container_guid,
            'dateText' => $this->get_date_text(),
            'imageURL' => $this->thumbnail_url,
            'snippetHTML' => $this->get_snippet()
        );
    }

    public function get_url()
    {
        $org = $this->get_container_entity();
        if ($org)
        {
            return $org->get_url() . "/post/" . $this->guid;
        }
        return '';
    }

    public function has_image()
    {
        return ($this->data_types & DataType::Image) != 0;
    }

    public function get_snippet($maxLength = 100)
    {
        return Markup::get_snippet($this->content, $maxLength);
    }
    
    function post_feed_items()
    {
        $org = $this->get_container_entity();
        $recent = time() - 60*60*6;
        
        $recent_update = $org->query_feed_items()
            ->where("action_name in ('news','newsmulti')")
            ->where('time_posted > ?', $recent)
            ->order_by('id desc')
            ->get();
        
        if ($recent_update)
        {
            $time = time();
        
            foreach ($recent_update->query_items_in_group()->filter() as $r)
            {
                $r->action_name = 'newsmulti';
                $r->subject_guid = $this->guid;
                $r->time_posted = $time;
                $prev_count = @$r->args['count'] ?: 1;
                $r->args = array('count' => $prev_count + 1);
                $r->save();
            }
        }
        else
        {                
            post_feed_items($org, 'news', $this);
        }
    }                
}
