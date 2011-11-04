<?php
/*
 * A registry that maps unique string identifiers (subtype_id values) to PHP class names. 
 *
 * The subtype_id identifiers should be used whenever it is necessary to store/send the class
 * of an object outside the PHP environment, for example when storing the class in the database, 
 * or sending it to the browser. (Registering a subtype_id for a class is not necessary for
 * classes that are not used in this way.)
 *
 * All non-abstract Entity subclasses must register a subtype_id value, which allows the system 
 * to determine the PHP class for a given entity guid (via the 'subtype_id' column of the 'entities' table)
 *
 * Since these subtype_id identifiers are stored in the database, they should generally not be 
 * changed once created. However, the PHP class name can be changed easily without any data migration.
 * If it is necessary to change a subtype_id value that is already stored in the database, 
 * the 'subtype_aliases' config setting can be used to aid in data migration.
 *
 * Modules can register new entity types by calling ClassRegistry::register(
 * in their start.php file. Modules should namespace their subtype_ids to avoid conflicting
 * with subtype_ids defined in other modules.
 */
class ClassRegistry
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
        'core.subscription.email.registration' => "EmailSubscription_Registration",
        
        'core.file' => 'UploadedFile',

        'core.widget' => 'Widget',
        'core.widget.contact' => 'Widget_Contact',
        'core.widget.post' => 'Widget_Post',
        'core.widget.post.facebook' => 'Widget_FacebookPost',
        'core.widget.post.feed' => 'Widget_FeedItem',
        'core.widget.post.rss' => 'Widget_RSSItem',
        'core.widget.post.sms' => 'Widget_SMSPost',
        'core.widget.post.tweet' => 'Widget_Tweet',
        'core.widget.generic' => 'Widget_Generic',
        'core.widget.hardcoded' => 'Widget_Hardcoded',
        'core.widget.history' => 'Widget_History',
        'core.widget.home' => 'Widget_Home',
        'core.widget.links' => 'Widget_Links',
        'core.widget.location' => 'Widget_Location',
        'core.widget.menu' => 'Widget_Menu',
        'core.widget.mission' => 'Widget_Mission',
        'core.widget.news' => 'Widget_News',
        'core.widget.personprofile' => 'Widget_PersonProfile',
        'core.widget.projects' => 'Widget_Projects',
        'core.widget.sectors' => 'Widget_Sectors',
        'core.widget.team' => 'Widget_Team',
        'core.widget.updates' => 'Widget_Updates',       
        'core.widget.comment' => 'Comment',
        
        'core.feeditem.home.edit' => 'FeedItem_EditHome',
        'core.feeditem.widget.edit' => 'FeedItem_EditWidget',
        'core.feeditem.widget.new' => 'FeedItem_NewWidget',
        'core.feeditem.news' => 'FeedItem_News',
        'core.feeditem.news.multi' => 'FeedItem_NewsMulti',
        'core.feeditem.register' => 'FeedItem_Register',
        
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
    
    static function register($types)
    {
        foreach ($types as $subtype_id => $class_name)
        {
            static::$subtype_to_class[$subtype_id] = $class_name;
        }
    }
    
    static function get_class($subtype_id)
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