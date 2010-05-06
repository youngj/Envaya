<?php

class NewsUpdate extends ElggObject
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
    	if (!$this->language)
    	{
    		$this->language = guess_language($this->content);
    	}

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
        $file = new ElggFile();
        $file->owner_guid = $this->container_guid;
        $file->setFilename("news/{$this->guid}$size.jpg");
        return $file;
    }

    public function getTitle()
    {
        return elgg_echo("widget:news:item");
    }

    public function jsProperties()
    {
        return array(
            'guid' => $this->guid,
            'container_guid' => $this->container_guid,
            'dateText' => $this->getDateText(),
            'imageURL' => $this->getImageURL('small'),
            'snippetHTML' => elgg_view('output/text', array('value' => $this->getSnippet()))
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

    public function getImageURL($size = '')
    {
        return $this->hasImage() ? ($this->getImageFile($size)->getURL()."?{$this->time_updated}") : "";
    }

    public function hasImage()
    {
        return ($this->data_types & DataType::Image) != 0;
    }

    public function getSnippet($maxLength = 100)
    {
        return get_snippet($this->content, $maxLength);
    }

    public function getDateText()
    {
        return friendly_time($this->time_created);
    }

    static function getImageSizes()
    {
        return array(
            'small' => '100x100',
            'large' => '450x450',
        );
    }

    public static function all($limit = 10, $offset = 0)
    {
        return static::filterByCondition(array(), array(), 'time_created desc', $limit, $offset);
    }

    public static function filterByOrganizations($orgs, $limit = 10, $offset = 0)
    {
        if (empty($orgs))
        {
            return array();
        }
        else
        {
            $where = array();
            $args = array();
            $in = array();

            foreach ($orgs as $org)
            {
                $in[] = "?";
                $args[] = $org->guid;
            }

            $where[] = "container_guid IN (".implode(",", $in).")";

            return static::filterByCondition($where, $args, 'time_created desc', $limit, $offset);
        }
    }
}
