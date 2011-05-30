<?php
/*
 * A registry that maps unique string identifiers to Entity class names. 
 * This allows the system to determine the PHP class for a given entity guid.
 *
 * These string identifiers are stored in the 'entities' table in the 'subtype_id' column,
 * so they should generally not be changed once created.
 *
 * Modules can register new entity types by calling EntityRegistry::register_subtype 
 * in their start.php file. Modules should namespace their subtype_ids to avoid conflicting
 * with subtype_ids defined in other modules.
 */
class EntityRegistry
{
    private static $subtype_to_class = array(
        'core.user' => 'User',
        'core.user.org' => 'Organization',        
        'core.user.org.relation' => 'OrgRelationship',                                    
        'core.file' => 'UploadedFile',
        'core.widget' => 'Widget',
        'core.widget.comment' => 'Comment',
        'core.featured.site' => 'FeaturedSite',
        'core.featured.photo' => 'FeaturedPhoto',
        'core.discussion.message' => 'DiscussionMessage',
        'core.discussion.topic' => 'DiscussionTopic',
        'core.feed' => 'ExternalFeed',
        'core.feed.rss' => 'ExternalFeed_RSS',
        'core.feed.facebook' => 'ExternalFeed_Facebook',
        'core.feed.twitter' => 'ExternalFeed_Twitter',        
        'core.externalsite' => 'ExternalSite',
        'core.externalsite.facebook' => 'ExternalSite_Facebook',
        'core.externalsite.twitter' => 'ExternalSite_Twitter',
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