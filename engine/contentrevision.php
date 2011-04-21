<?php

class ContentRevision extends Model
{
    const Draft = 0;
    const Published = 1;
    
    static $table_name = 'revisions';
    
    static $table_attributes = array(
        'owner_guid' => 0,
        'entity_guid' => 0,
        'time_created' => 0,        
        'time_updated' => 0,        
        'content' => '',
        'status' => 0,
    );    
    
    function can_edit()
    {
        return $this->get_entity()->can_edit();
    }
    
    function get_entity()
    {
        return Entity::get_by_guid($this->entity_guid);
    }
    
    static function get_recent_draft($entity)
    {
        $time = time();    
    
        $revision = ContentRevision::query()
            ->where('entity_guid = ?', $entity->guid)
            ->where('time_created > ? ', $time - 15 * 60)
            ->where('status = ?', ContentRevision::Draft)
            ->order_by('time_created desc')
            ->get();
        
        if (!$revision)
        {
            $revision = new ContentRevision();
            $revision->owner_guid = $entity->owner_guid;
            $revision->entity_guid = $entity->guid;
            $revision->time_created = $time;
            $revision->status = ContentRevision::Draft;        
        }            
        return $revision;
    }    
            
    function js_properties()
    {
        return array(
            'id' => $this->id,
            'time_updated' => $this->time_updated,
            'friendly_time' => friendly_time($this->time_updated),
            'status' => $this->status,
        );
    }
}
