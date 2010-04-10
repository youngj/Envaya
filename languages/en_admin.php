<?php     
    $en_admin = array(
        'item:object:widget' => "Pages",                       
        'item:object:blog' => 'News updates',
        'item:object:partnership' => 'Partnerships',
        'item:user:organization' => "Organizations",
        'item:object:translation' => "Translations",
        'item:object:team_member' => "Team members",
        'item:user' => "Users",

        'admin_option' => "Make this user an admin?",
        
        'approval:approve' => "Approve Organization",
        'approval:unapprove' => "Remove Approval",
        'approval:reject' => "Reject Organization",
        'approval:delete' => "Delete Organization",
        'approval:unreject' => "Remove Rejection",
        'approval:changed' => "Organization approval changed",        
        
        'admin:configuration:success' => "Your settings have been saved.",
        'admin:configuration:fail' => "Your settings could not be saved.",

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
        'admin:statistics:label:version' => "Elgg version",
        'admin:statistics:label:version:release' => "Release",
        'admin:statistics:label:version:version' => "Version",

        'admin:user:label:search' => "Find users:",
        'admin:user:label:seachbutton' => "Search",

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
        'SecurityException:Codeblock' => "Denied access to execute privileged code block",
        'DatabaseException:WrongCredentials' => "Elgg couldn't connect to the database using the given credentials.",
        'DatabaseException:NoConnect' => "Elgg couldn't select the database '%s', please check that the database is created and you have access to it.",
        'SecurityException:FunctionDenied' => "Access to privileged function '%s' is denied.",
        'DatabaseException:DBSetupIssues' => "There were a number of issues: ",
        'DatabaseException:ScriptNotFound' => "Elgg couldn't find the requested database script at %s.",

        'IOException:FailedToLoadGUID' => "Failed to load new %s from GUID:%d",
        'InvalidParameterException:NonElggObject' => "Passing a non-ElggObject to an ElggObject constructor!",
        'InvalidParameterException:UnrecognisedValue' => "Unrecognised value passed to constuctor.",

        'InvalidClassException:NotValidElggStar' => "GUID:%d is not a valid %s",

        'PluginException:MisconfiguredPlugin' => "%s is a misconfigured plugin.",

        'InvalidParameterException:NonElggUser' => "Passing a non-ElggUser to an ElggUser constructor!",

        'IOException:UnableToSaveNew' => "Unable to save new %s",

        'ConfigurationException:NoCachePath' => "Cache path set to nothing!",
        'IOException:NotDirectory' => "%s is not a directory.",

        'IOException:BaseEntitySaveFailed' => "Unable to save new object's base entity information!",
        'InvalidParameterException:EntityTypeNotSet' => "Entity type must be set.",

        'ClassException:ClassnameNotClass' => "%s is not a %s.",
        'InstallationException:TypeNotSupported' => "Type %s is not supported. This indicates an error in your installation, most likely caused by an incomplete upgrade.",

        'InvalidParameterException:UnrecognisedFileMode' => "Unrecognised file mode '%s'",
        'InvalidParameterException:MissingOwner' => "File %s (%d) is missing an owner!",
        'IOException:CouldNotMake' => "Could not make %s",
        'IOException:MissingFileName' => "You must specify a name before opening a file.",
        'ClassNotFoundException:NotFoundNotSavedWithFile' => "Filestore not found or class not saved with file!",
        'NotificationException:NoHandlerFound' => "No handler found for '%s' or it was not callable.",
        'NotificationException:NoEmailAddress' => "Could not get the email address for GUID:%d",
        'NotificationException:MissingParameter' => "Missing a required parameter, '%s'",

        'DatabaseException:UnspecifiedQueryType' => "Unrecognised or unspecified query type.",

        'InvalidParameterException:NoEntityFound' => "No entity found, it either doesn't exist or you don't have access to it.",

        'SecurityException:APIAccessDenied' => "Sorry, API access has been disabled by the administrator.",
        'SecurityException:NoAuthMethods' => "No authentication methods were found that could authenticate this API request.",
        'APIException:ApiResultUnknown' => "API Result is of an unknown type, this should never happen.",

        'NotImplementedException:XMLRPCMethodNotImplemented' => "XML-RPC method call '%s' not implemented.",
        'InvalidParameterException:UnexpectedReturnFormat' => "Call to method '%s' returned an unexpected result.",
        'CallException:NotRPCCall' => "Call does not appear to be a valid XML-RPC call",

        'CronException:unknownperiod' => '%s is not a recognised period.',

        'SecurityException:deletedisablecurrentsite' => 'You can not delete or disable the site you are currently viewing!',

        'xmlrpc:noinputdata'    =>  "Input data missing",


        'comments:count' => "%s comments",

        'generic_comments:add' => "Add a comment",
        'generic_comments:text' => "Comment",
        'generic_comment:posted' => "Your comment was successfully posted.",
        'generic_comment:deleted' => "Your comment was successfully deleted.",
        'generic_comment:blank' => "Sorry; you need to actually put something in your comment before we can save it.",
        'generic_comment:notfound' => "Sorry; we could not find the specified item.",
        'generic_comment:notdeleted' => "Sorry; we could not delete this comment.",
        'generic_comment:failure' => "An unexpected error occurred when adding your comment. Please try again.",

        'generic_comment:email:subject' => 'You have a new comment!',
        'generic_comment:email:body' => "You have a new comment on your item \"%s\" from %s. It reads:


%s


To reply or view the original item, click here:

    %s

To view %s's profile, click here:

    %s

You cannot reply to this email.",

        "aa" => "Afar",
        "ab" => "Abkhazian",
        "af" => "Afrikaans",
        "am" => "Amharic",
        "ar" => "Arabic",
        "as" => "Assamese",
        "ay" => "Aymara",
        "az" => "Azerbaijani",
        "ba" => "Bashkir",
        "be" => "Byelorussian",
        "bg" => "Bulgarian",
        "bh" => "Bihari",
        "bi" => "Bislama",
        "bn" => "Bengali; Bangla",
        "bo" => "Tibetan",
        "br" => "Breton",
        "ca" => "Catalan",
        "co" => "Corsican",
        "cs" => "Czech",
        "cy" => "Welsh",
        "da" => "Danish",
        "de" => "German",
        "dz" => "Bhutani",
        "el" => "Greek",        
        "eo" => "Esperanto",
        "es" => "Spanish",
        "et" => "Estonian",
        "eu" => "Basque",
        "fa" => "Persian",
        "fi" => "Finnish",
        "fj" => "Fiji",
        "fo" => "Faeroese",
        "fr" => "French",
        "fy" => "Frisian",
        "ga" => "Irish",
        "gd" => "Scots / Gaelic",
        "gl" => "Galician",
        "gn" => "Guarani",
        "gu" => "Gujarati",
        "he" => "Hebrew",
        "ha" => "Hausa",
        "hi" => "Hindi",
        "hr" => "Croatian",
        "hu" => "Hungarian",
        "hy" => "Armenian",
        "ia" => "Interlingua",
        "id" => "Indonesian",
        "ie" => "Interlingue",
        "ik" => "Inupiak",
        //"in" => "Indonesian",
        "is" => "Icelandic",
        "it" => "Italian",
        "iu" => "Inuktitut",
        "iw" => "Hebrew (obsolete)",
        "ja" => "Japanese",
        "ji" => "Yiddish (obsolete)",
        "jw" => "Javanese",
        "ka" => "Georgian",
        "kk" => "Kazakh",
        "kl" => "Greenlandic",
        "km" => "Cambodian",
        "kn" => "Kannada",
        "ko" => "Korean",
        "ks" => "Kashmiri",
        "ku" => "Kurdish",
        "ky" => "Kirghiz",
        "la" => "Latin",
        "ln" => "Lingala",
        "lo" => "Laothian",
        "lt" => "Lithuanian",
        "lv" => "Latvian/Lettish",
        "mg" => "Malagasy",
        "mi" => "Maori",
        "mk" => "Macedonian",
        "ml" => "Malayalam",
        "mn" => "Mongolian",
        "mo" => "Moldavian",
        "mr" => "Marathi",
        "ms" => "Malay",
        "mt" => "Maltese",
        "my" => "Burmese",
        "na" => "Nauru",
        "ne" => "Nepali",
        "nl" => "Dutch",
        "no" => "Norwegian",
        "oc" => "Occitan",
        "om" => "(Afan) Oromo",
        "or" => "Oriya",
        "pa" => "Punjabi",
        "pl" => "Polish",
        "ps" => "Pashto / Pushto",
        "pt" => "Portuguese",
        "qu" => "Quechua",
        "rm" => "Rhaeto-Romance",
        "rn" => "Kirundi",
        "ro" => "Romanian",
        "ru" => "Russian",
        "rw" => "Kinyarwanda",
        "sa" => "Sanskrit",
        "sd" => "Sindhi",
        "sg" => "Sangro",
        "sh" => "Serbo-Croatian",
        "si" => "Singhalese",
        "sk" => "Slovak",
        "sl" => "Slovenian",
        "sm" => "Samoan",
        "sn" => "Shona",
        "so" => "Somali",
        "sq" => "Albanian",
        "sr" => "Serbian",
        "ss" => "Siswati",
        "st" => "Sesotho",
        "su" => "Sundanese",
        "sv" => "Swedish",        
        "ta" => "Tamil",
        "te" => "Tegulu",
        "tg" => "Tajik",
        "th" => "Thai",
        "ti" => "Tigrinya",
        "tk" => "Turkmen",
        "tl" => "Tagalog",
        "tn" => "Setswana",
        "to" => "Tonga",
        "tr" => "Turkish",
        "ts" => "Tsonga",
        "tt" => "Tatar",
        "tw" => "Twi",
        "ug" => "Uigur",
        "uk" => "Ukrainian",
        "ur" => "Urdu",
        "uz" => "Uzbek",
        "vi" => "Vietnamese",
        "vo" => "Volapuk",
        "wo" => "Wolof",
        "xh" => "Xhosa",
        //"y" => "Yiddish",
        "yi" => "Yiddish",
        "yo" => "Yoruba",
        "za" => "Zuang",
        "zh" => "Chinese",
        "zu" => "Zulu",

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
        
        'actionundefined' => "The requested action (%s) was not defined in the system.",
        'pageownerunavailable' => 'Warning: The page owner %d is not accessible!',

        'profile' => "Profile",
        'profile:edit:default' => 'Replace profile fields',
        'profile:preview' => 'Preview',

        'profile:yours' => "Your profile",
        'profile:user' => "%s's profile",

        'profile:edit' => "Edit profile",
        'profile:profilepictureinstructions' => "The profile picture is the image that's displayed on your profile page. <br /> You can change it as often as you'd like. (File formats accepted: GIF, JPG or PNG)",
        'profile:icon' => "Profile picture",
        'profile:createicon' => "Create your avatar",
        'profile:currentavatar' => "Current avatar",
        'profile:createicon:header' => "Profile picture",
        'profile:profilepicturecroppingtool' => "Profile picture cropping tool",
        'profile:createicon:instructions' => "Click and drag a square below to match how you want your picture cropped.  A preview of your cropped picture will appear in the box on the right.  When you are happy with the preview, click 'Create your avatar'. This cropped image will be used throughout the site as your avatar. ",

        'profile:editdetails' => "Edit details",
        'profile:editicon' => "Edit profile icon",

        'profile:aboutme' => "About me",
        'profile:description' => "About me",
        'profile:briefdescription' => "Brief description",
        'profile:location' => "Location",
        'profile:skills' => "Skills",
        'profile:interests' => "Interests",
        'profile:contactemail' => "Contact email",
        'profile:phone' => "Telephone",
        'profile:mobile' => "Mobile phone",
        'profile:website' => "Website",

        'profile:banned' => 'This user account has been suspended.',
        'profile:deleteduser' => 'Deleted user',

        'profile:label' => "Profile label",
        'profile:type' => "Profile type",

        'profile:saved' => "Your profile was successfully saved.",
        'profile:icon:uploaded' => "Your profile picture was successfully uploaded.",

        'profile:noaccess' => "You do not have permission to edit this profile.",
        'profile:notfound' => "Sorry; we could not find the specified profile.",
        'profile:cantedit' => "Sorry; you do not have permission to edit this profile.",
        'profile:icon:notfound' => "Sorry; there was a problem uploading your profile picture.",        
        
        'user' => "User",
        'feed:rss' => 'Subscribe to feed',

        'notifications:usersettings' => "Notification settings",
        'notifications:methods' => "Please specify which methods you want to permit.",

        'notifications:usersettings:save:ok' => "Your notification settings were successfully saved.",
        'notifications:usersettings:save:fail' => "There was a problem saving your notification settings.",        
        
        'viewtype:change' => "Change listing type",
        'viewtype:list' => "List view",
        'viewtype:gallery' => "Gallery",

        'tag:search:startblurb' => "Items with tags matching '%s':",

        'user:search:startblurb' => "Users matching '%s':",
        'user:search:finishblurb' => "To view more, click here.",
        
        'firstadminlogininstructions' => 'Your new Elgg site has been successfully installed and your administrator account created. You can now configure your site further by enabling various installed plugin tools.',        

        'invite' => "Invite",

        'resetpassword' => "Reset password",
        'makeadmin' => "Make admin",
        'removeadmin' => "Remove admin",

        'unknown' => 'Unknown',

        'active' => 'Active',
        'total' => 'Total',

        'content' => "content",
        'content:latest' => 'Latest activity',

        'installation:error:htaccess' => "Elgg requires a file called .htaccess to be set in the root directory of its installation. We tried to create it for you, but Elgg doesn't have permission to write to that directory.

Creating this is easy. Copy the contents of the textbox below into a text editor and save it as .htaccess

",
            'installation:error:settings' => "Elgg couldn't find its settings file. Most of Elgg's settings will be handled for you, but we need you to supply your database details. To do this:

1. Rename engine/settings.example.php to settings.php in your Elgg installation.

2. Open it with a text editor and enter your MySQL database details. If you don't know these, ask your system administrator or technical support for help.

Alternatively, you can enter your database settings below and we will try and do this for you...",

        'installation' => "Installation",
        'installation:success' => "Elgg's database was installed successfully.",
        'installation:configuration:success' => "Your initial configuration settings have been saved. Now register your initial user; this will be your first system administrator.",

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
        
        'actiongatekeeper:missingfields' => 'Form is missing __token or __ts fields',
    );  
    
    global $CONFIG;
    $CONFIG->en_admin = $en_admin;
    
    add_translation("en",$en_admin);