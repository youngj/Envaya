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
        'core.user.person' => 'Person',
        
        'core.scope' => 'UserScope',  
        
        'core.permission' => 'Permission',
        'core.permission.changeuserapproval' => 'Permission_ChangeUserApproval',
        'core.permission.editcomment' => 'Permission_EditComment',
        'core.permission.viewusersettings' => 'Permission_ViewUserSettings',
        'core.permission.editusersettings' => 'Permission_EditUserSettings',
        'core.permission.viewusersite' => 'Permission_ViewUserSite',
        'core.permission.editusersite' => 'Permission_EditUserSite',
        'core.permission.sendmessage' => 'Permission_SendMessage',
        'core.permission.useadmintools' => 'Permission_UseAdminTools',
        'core.permission.viewoutgoingmessage' => 'Permission_ViewOutgoingMessage',

        'core.subscription.sms' => "SMSSubscription",
        'core.subscription.sms.comments' => "SMSSubscription_Comments",
        'core.subscription.sms.news' => "SMSSubscription_News",
        'core.subscription.email' => "EmailSubscription",
        'core.subscription.email.comments' => "EmailSubscription_Comments",
        
        'core.file' => 'UploadedFile',
        
        'core.widget' => 'Widget',
        'core.widget.comment' => 'Comment',
        
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
    
    static function register_subtypes($subtypes)
    {
        foreach ($subtypes as $subtype_id => $class_name)
        {
            static::$subtype_to_class[$subtype_id] = $class_name;
        }
    }
    
    static function get_subtype_class($subtype_id)
    {
        $cls = @static::$subtype_to_class[$subtype_id];
        if ($cls)
        {
            return $cls;
        }
        
        $aliases = Config::get('subtype_aliases');
        if ($aliases)
        {
            return @$aliases[$subtype_id];
        }
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