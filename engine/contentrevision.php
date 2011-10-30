<?php

class ContentRevision extends Model
{
    static $table_name = 'revisions';
    
    static $table_attributes = array(
        'owner_guid' => 0,
        'entity_guid' => 0,
        'time_created' => 0,        
        'time_updated' => 0,        
        'content' => '',
        'publish_status' => 0,
    );    
        
    function get_entity()
    {
        return Entity::get_by_guid($this->entity_guid, true);
    }
    
    static function query_drafts($entity)
    {
        return ContentRevision::query()
            ->where('entity_guid = ?', $entity->guid)
            ->order_by('time_updated desc, id desc');
    }
    
    static function get_recent_draft($entity)
    {
        $time = timestamp();    
    
        if (!$entity->guid)
        {
            throw new InvalidParameterException("entity guid not set in get_recent_draft");
        }
         
        $revision = ContentRevision::query()
            ->where('entity_guid = ?', $entity->guid)
            ->where('time_created > ? ', $time - 15 * 60)
            ->where('publish_status = ?', Widget::Draft)
            ->order_by('time_created desc')
            ->get();
        
        if (!$revision)
        {
            $revision = new ContentRevision();
            $revision->owner_guid = $entity->owner_guid;
            $revision->entity_guid = $entity->guid;
            $revision->time_created = $time;
            $revision->publish_status = Widget::Draft;        
        }            
        return $revision;
    }    
            
    function js_properties()
    {
        return array(
            'id' => $this->id,
            'time_updated' => $this->time_updated,
            'friendly_time' => friendly_time($this->time_updated),
            'publish_status' => $this->publish_status,
        );
    }
}
