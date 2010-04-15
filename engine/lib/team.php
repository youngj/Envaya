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

    static function getImageSizes()
    {
        return array(
            'small' => '150x150',
            'large' => '450x450',
        );
    }    
}