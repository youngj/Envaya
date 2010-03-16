<?php

class Widget extends ElggObject
{
    static $subtype_id = T_widget;
    static $table_name = 'widgets';
    static $table_attributes = array(
        'widget_name' => 0,
        'content' => '',
        'data_types' => 0,
    );        
    
    function renderView()
    {
        $res = elgg_view("widgets/{$this->widget_name}_view", array('widget' => $this));
        if ($res)
        {
            return $res;
        }    
        return elgg_view("widgets/generic_view", array('widget' => $this));    
    }
    
    function renderEdit()
    {
        $res = elgg_view("widgets/{$this->widget_name}_edit", array('widget' => $this));
        if ($res)
        {
            return $res;
        }    
        return elgg_view("widgets/generic_edit", array('widget' => $this));
    } 
    
    function getURL()
    {
        $org = $this->getContainerEntity();
        return "{$org->getUrl()}/{$this->widget_name}";
    }
    
    function saveInput()
    {
        $fn = "save_widget_{$this->widget_name}";
        if (!is_callable($fn))
        {
            $fn = "save_widget";
        }
        $fn($this);
    }    

    public function getImageFile($size = '')
    {
        $filehandler = new ElggFile();
        $filehandler->owner_guid = $this->container_guid;
        $filehandler->setFilename("widget/{$this->guid}$size.jpg");
        return $filehandler;       
    }
    
    public function hasImage()
    {
        return ($this->data_types & DataType::Image) != 0;
    }   
    
    public function getImageURL($size = 'large')
    {
        return "{$this->getUrl()}/image/{$size}?{$this->time_updated}";
    }

    public function setImage($imageData)
    {
        if (!$imageData)
        {
            $this->data_types &= ~DataType::Image;     
        }
        else
        {
            $this->data_types |= DataType::Image; 

            $prefix = "widget/{$this->guid}";

            $file = new ElggFile();
            $file->owner_guid = $this->container_guid;
            $file->container_guid = $this->guid;

            $file->setFilename("{$prefix}.jpg");
            $file->open("write");
            $file->write($imageData);
            $file->close();

            $originalFileName = $file->getFilenameOnFilestore();

            $thumbsmall = get_resized_image_from_existing_file($originalFileName,100,150, false);
            if ($thumbsmall) 
            {
                $file->setFilename("{$prefix}small.jpg");
                $file->open("write");
                $file->write($thumbsmall);
                $file->close();
            }            

            $thumbmed = get_resized_image_from_existing_file($originalFileName,200,300, false);
            if ($thumbmed) 
            {
                $file->setFilename("{$prefix}medium.jpg");
                $file->open("write");
                $file->write($thumbmed);
                $file->close();
            }
            
            $thumblarge = get_resized_image_from_existing_file($originalFileName,450,450, false);
            if ($thumblarge) 
            {
                $file->setFilename("{$prefix}large.jpg");
                $file->open("write");
                $file->write($thumblarge);
                $file->close();
            }
            
        }   
        $this->save();
    } 
    
    public function isActive()
    {
        return $this->guid && $this->isEnabled();
    }
}

function save_widget($widget)
{
    $widget->content = get_input('content');
    $widget->image_position = get_input('image_position');
    $widget->save();
    
    if (has_uploaded_file('image'))
    {            
        if (is_image_upload('image'))
        {    
            $widget->setImage(get_uploaded_file('image'));        
        }
        else
        {
            register_error(elgg_echo('upload:invalid_image'));
        }
    }    
    else if (get_input('deleteimage'))
    {
        $widget->setImage(null);
    }
}

function save_widget_home($widget)
{
    $widget->content = get_input('content');
    $org = $widget->getContainerEntity();    
    $org->setSectors(get_input_array('sector'));
    $org->sector_other = get_input('sector_other');
    $org->save();
    $widget->save();
}

function save_widget_map($widget)
{
    $org = $widget->getContainerEntity();
    $org->latitude = get_input('org_lat');
    $org->longitude = get_input('org_lng');    
    $org->save();
    
    $widget->zoom = get_input('map_zoom');    
    $widget->save();
}

function save_widget_contact($widget)
{
    $org = $widget->getContainerEntity();
    $widget->public_email = get_input('public_email');
    $org->phone_number = get_input('phone_number');    
    $org->contact_name = get_input('contact_name');    
    $org->contact_title = get_input('contact_title');    
    $org->save();
    $widget->save();
}
