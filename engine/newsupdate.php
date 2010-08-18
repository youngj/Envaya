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
            post_feed_items($this->getContainerEntity(), 'news', $this);
        }
        return $res;
    }

    public function getImageFile($size = '')
    {
        $file = new UploadedFile();
        $file->owner_guid = $this->container_guid;
        $file->filename = "news/{$this->guid}$size.jpg";
        return $file;
    }

    public function getTitle()
    {
        return __("widget:news:item");
    }

    public function jsProperties()
    {
        return array(
            'guid' => $this->guid,
            'container_guid' => $this->container_guid,
            'dateText' => $this->getDateText(),
            'imageURL' => $this->thumbnail_url,
            'snippetHTML' => $this->getSnippet()
        );
    }

    public function getURL()
    {
        $org = $this->getContainerEntity();
        if ($org)
        {
            return $org->getUrl() . "/post/" . $this->getGUID();
        }
        return '';
    }

    public function hasImage()
    {
        return ($this->data_types & DataType::Image) != 0;
    }

    public function getSnippet($maxLength = 100)
    {
        return Markup::get_snippet($this->content, $maxLength);
    }

    public function getDateText()
    {
        return friendly_time($this->time_created);
    }
}
