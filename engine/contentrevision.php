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
            $revision->owner_guid = $entity->container_guid;
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
            'content' => $this->content,
            'time_updated' => $this->time_updated,
            'friendly_time' => friendly_time($this->time_updated),
            'status' => $this->status,
        );
    }
}
