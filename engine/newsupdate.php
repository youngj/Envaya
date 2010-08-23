<?php

class NewsUpdate extends Entity
{
    static $subtype_id = T_blog;
    static $table_name = 'news_updates';
    static $table_attributes = array(
        'content' => '',
        'data_types' => 0,
        'language' => '',
    );

    public function save()
    {
        $isNew = (!$this->guid);

        $res = parent::save();

        if ($res && $isNew)
        {
            post_feed_items($this->get_container_entity(), 'news', $this);
        }
        return $res;
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

    public function get_date_text()
    {
        return friendly_time($this->time_created);
    }
}
