<?php     
return array(
    'item:widget' => "Pages",                       
    'item:blog' => 'News updates',
    'item:partnership' => 'Partnerships',
    'item:organization' => "Organizations",
    'item:translation' => "Translations",    
    'item:user' => "Users",
    
    'email:registernotify:subject' => "New organization registered: %s",
    'email:registernotify:body' => "To view their website and approve or reject it, visit\n%s",
    
    'username:changed' => "Username changed",
    'username:current' => "Current username",
    'username:new' => "New username",
    'username:title' => "Change username",

    'admin_option' => "Make this user an admin?",
    
    'approval:approve' => "Approve Organization",
    'approval:unapprove' => "Remove Approval",
    'approval:reject' => "Reject Organization",
    'approval:delete' => "Delete Organization",
    'approval:unreject' => "Remove Rejection",
    'approval:changed' => "Organization approval changed",        
    
    'admin' => "Administration",
    'admin:description' => "The admin panel allows you to control all aspects of the system, from user management to how plugins behave. Choose an option below to get started.",

    'admin:user' => "User Administration",
    'admin:user:description' => "This admin panel allows you to control user settings for your site. Choose an option below to get started.",
    'admin:user:adduser:label' => "Click here to add a new user...",
    'admin:user:opt:linktext' => "Configure users...",
    'admin:user:opt:description' => "Configure users and account information. ",

    'admin:statistics' => "Statistics",
    'admin:statistics:description' => "This is an overview of statistics on your site. If you need more detailed statistics, a professional administration feature is available.",
    'admin:statistics:opt:description' => "View statistical information about users and objects on your site.",
    'admin:statistics:opt:linktext' => "View statistics...",
    'admin:statistics:label:basic' => "Basic site statistics",
    'admin:statistics:label:numentities' => "Entities on site",
    'admin:statistics:label:numusers' => "Number of users",
    'admin:statistics:label:numonline' => "Number of users online",
    'admin:statistics:label:onlineusers' => "Users online now",

    'admin:user:label:search' => "Find users:",

    'admin:user:ban:no' => "Can not ban user",
    'admin:user:ban:yes' => "User banned.",
    'admin:user:unban:no' => "Can not unban user",
    'admin:user:unban:yes' => "User un-banned.",
    'admin:user:delete:no' => "Can not delete user",
    'admin:user:delete:yes' => "User deleted",

    'admin:user:resetpassword:yes' => "Password reset, user notified.",
    'admin:user:resetpassword:no' => "Password could not be reset.",

    'admin:user:makeadmin:yes' => "User is now an admin.",
    'admin:user:makeadmin:no' => "We could not make this user an admin.",

    'admin:user:removeadmin:yes' => "User is no longer an admin.",
    'admin:user:removeadmin:no' => "We could not remove administrator privileges from this user.",
    
    'error:NoConnect' => "Couldn't connect to the database.",        
    'error:FailedToLoadGUID' => "Failed to load new %s from GUID:%d",
    'error:UnrecognisedValue' => "Unrecognised value passed to constuctor.",
    'error:NotValidEntity' => "GUID:%d is not a valid %s",
    'error:BaseEntitySaveFailed' => "Unable to save new object's base entity information!",
    'error:EntityTypeNotSet' => "Entity type must be set.",

    'error:ClassnameNotClass' => "%s is not a %s.",

    'error:NoHandlerFound' => "No handler found for '%s' or it was not callable.",
    'error:NoEmailAddress' => "Could not get the email address for GUID:%d",
    'error:MissingParameter' => "Missing a required parameter, '%s'",

    'error:InvalidQueryParameter' => "Invalid query parameter %s",

    'users:searchtitle' => "Searching for users: %s",
    'groups:searchtitle' => "Searching for groups: %s",
    'advancedsearchtitle' => "%s with results matching %s",        

    'adduser' => "Add User",
    'adduser:ok' => "You have successfully added a new user.",
    'adduser:bad' => "The new user could not be created.",

    'entity:default:missingsupport:popup' => 'This entity cannot be displayed correctly. This may be because it requires support provided by a plugin that is no longer installed.',
    'entity:delete:success' => 'Entity %s has been deleted',
    'entity:delete:fail' => 'Entity %s could not be deleted',

    'logbrowser' => 'Log browser',
    'logbrowser:browse' => 'Browse system log',
    'logbrowser:search' => 'Refine results',
    'logbrowser:user' => 'Username to search by',
    'logbrowser:starttime' => 'Beginning time (for example "last monday", "1 hour ago")',
    'logbrowser:endtime' => 'End time',

    'logbrowser:explore' => 'Explore log',
    
    'pageownerunavailable' => 'Warning: The page owner %d is not accessible!',

    'user' => "User",
    'feed:rss' => 'Subscribe to feed',

    'notifications:usersettings' => "Notification settings",
    'notifications:methods' => "Please specify which methods you want to permit.",

    'notifications:usersettings:save:ok' => "Your notification settings were successfully saved.",
    'notifications:usersettings:save:fail' => "There was a problem saving your notification settings.",        
    
    'viewtype:change' => "Change listing type",
    'viewtype:list' => "List view",
    'viewtype:gallery' => "Gallery",

    'search:startblurb' => "Items matching '%s':",

    'user:search:startblurb' => "Users matching '%s':",
    'user:search:finishblurb' => "To view more, click here.",
    
    'invite' => "Invite",

    'resetpassword' => "Reset password",
    'makeadmin' => "Make admin",
    'removeadmin' => "Remove admin",

    'unknown' => 'Unknown',

    'active' => 'Active',
    'total' => 'Total',

    'content' => "content",
    'content:latest' => 'Latest activity',

    'accept' => "Accept",
    'load' => "Load",
    'upload' => "Upload",
    'ban' => "Ban",
    'unban' => "Unban",
    'enable' => "Enable",
    'disable' => "Disable",
    'request' => "Request",
    'complete' => "Complete",
    'open' => 'Open',
    'close' => 'Close',
    'reply' => "Reply",
    'more' => 'More',
    'comments' => 'Comments',     
    
    'featured:active' => '(Active)',
    'featured:activate' => 'Activate',
    'featured:edit' => 'Edit featured site',
    'featured:add' => 'Add featured site',
    
    'featured_photo:add' => "Add featured photo",
    
    'widget:invalid_class' => 'The handler %s was not found.',
    'widget:options' => 'Page Options',
    'widget:handler' => 'Page Handler (PHP Class Name)',
    'widget:handler_arg' => 'Page Handler Argument (optional)',
    'widget:menu_order' => 'Menu Order',
    'widget:in_menu' => 'Show in Menu?',
);  
