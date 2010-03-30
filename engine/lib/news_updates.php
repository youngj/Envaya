<?php

class NewsUpdate extends ElggObject
{
    static $subtype_id = T_blog;
    static $table_name = 'news_updates';
    static $table_attributes = array(
        'content' => '',
        'data_types' => 0,
    );        
    
    public function getImageFile($size = '')
    {
        $file = new ElggFile();
        $file->owner_guid = $this->container_guid;
        $file->setFilename("news/{$this->guid}$size.jpg");
        return $file;       
    }
    
    public function jsProperties()
    {
        return array(
            'guid' => $this->guid,
            'container_guid' => $this->container_guid,
            'dateText' => $this->getDateText(),
            'imageURL' => $this->getImageURL('small'),
            'snippetHTML' => $this->getSnippetHTML()
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
    
    public function getSnippetHTML($maxLength = 100)
    {
        $content = $this->content;
        if ($content)
        {
            // todo: multi-byte support
            if (strlen($content) > $maxLength)
            {
                $content = substr($content, 0, $maxLength) . "...";
            }                
            
            return elgg_view('output/text', array('value' => $content));
        }
        return '';
    }

    public function getDateText()
    {
        return friendly_time($this->time_created); 
    }
    
    public function setImage($imageFilePath)
    {
        if (!$imageFilePath)
        {
            $this->data_types &= ~DataType::Image;     
        }
        else
        {        
            if ($this->getImageFile('small')->uploadFile(resize_image_file($imageFilePath,100,100))
               && $this->getImageFile('large')->uploadFile(resize_image_file($imageFilePath,450,450)))
            {
                $this->data_types |= DataType::Image;  
            }
            else            
            {
                throw new DataFormatException("error saving image");
            }            
        }   
        $this->save();
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
