<?php

class EntityStatus
{
    const Disabled = 0; // basically deleted, except the db row still exists so we can undelete
    const Enabled = 1;
    const Draft = 2;
}

class EntityRegistry
{
    private static $subtype_to_class = array(
        'core.user' => 'User',
        'core.user.org' => 'Organization',        
        'core.user.org.relation' => 'OrgRelationship',                                    
        'core.file' => 'UploadedFile',
        'core.widget' => 'Widget',
        'core.widget.comment' => 'Comment',
        'core.email.template' => 'EmailTemplate',
        'core.featured.site' => 'FeaturedSite',
        'core.featured.photo' => 'FeaturedPhoto',
        'core.discussion.message' => 'DiscussionMessage',
        'core.discussion.topic' => 'DiscussionTopic',
    );
    private static $class_to_subtype = null;
    
    static function all_classes()
    {
        return static::$subtype_to_class;
    }
    
    static function register_subtype($subtype_id, $class_name)
    {
        static::$subtype_to_class[$subtype_id] = $class_name;
    }
    
    static function get_subtype_class($subtype_id)
    {
        return @static::$subtype_to_class[$subtype_id];
    }
    
    static function get_subtype_id($class_name)
    {
        if (static::$class_to_subtype == null)
        {
            static::$class_to_subtype = array_flip(static::$subtype_to_class);
        }
        return @static::$class_to_subtype[$class_name];
    }
}