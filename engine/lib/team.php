<?php

class TeamMember extends ElggObject
{
    static $subtype_id = T_team_member;
    static $table_name = 'team_members';
    
    static $table_attributes = array(
        'name' => '',
        'description' => '',
        'data_types' => 0,
        'list_order' => 0
    );        

    public function getImageFile($size = '')
    {
        $file = new ElggFile();
        $file->owner_guid = $this->container_guid;
        $file->setFilename("team/{$this->guid}$size.jpg");
        return $file;       
    }
    
    public function getEditURL()
    {
        return "{$this->getContainerEntity()->getURL()}/teammember/{$this->guid}/edit";
    }
    
    public function getImageURL($size = '')
    {
        return $this->hasImage() ? ($this->getImageFile($size)->getURL()."?{$this->time_updated}") : "";
    }        

    public function hasImage()
    {
        return ($this->data_types & DataType::Image) != 0;
    }   

    public function setImage($imageFilePath)
    {
        if (!$imageFilePath)
        {
            $this->data_types &= ~DataType::Image;     
        }
        else
        {        
            if ($this->getImageFile('small')->uploadFile(resize_image_file($imageFilePath,150,150))
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
    
}