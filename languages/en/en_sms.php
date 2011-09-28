<?php

return array(
    'sms:help' => "Envaya SMS",
    'sms:help_more' => "MORE=more text",
    'sms:help_next' => "NEXT=next page",
    'sms:help_page' => "1-%s=goto page",
    'sms:help_c' => "C=post comment",
    'sms:help_g' => "G%s=view comment",
    'sms:help_p' => "P=publish news",
    'sms:help_f' => 'F=find orgs',
    'sms:help_i' => 'I=contact info',
    'sms:help_n' => 'N=latest news',
    'sms:help_l' => 'L=set language',
    'sms:help_in' => 'IN=log in',
    'sms:help_out' => 'OUT=log out',
    
    'sms:bad_command' => 'Unknown command for Envaya SMS. Txt "HELP" for a list of commands.',
    'sms:bad_user' => "The username '{username}' does not exist on Envaya.",
    'sms:unapproved_user' => "The username '{username}' is awaiting approval and not yet visible to the public.",

    'sms:no_more_content' => "No more text available.",
    
    'sms:user_details' => 'Txt "I [user]" for details',
    'sms:user_news' => "N=news",
    'sms:user_discussions' => "Txt \"D\" for discussions",
    'sms:news_comments' => "%scmt@\"G[num]\"",
    'sms:news_one_comment' => "1cmt@\"G1\"",
    'sms:news_no_comments' => "0cmt:\"C [msg]\"",
    'sms:no_orgs_near' => "No organizations found near '%s'",
    'sms:no_orgs_name' => "No organizations found with name '%s'",
    'sms:no_more_orgs' => "No more organizations found.",
    
    'sms:no_news' => "%s has no news yet.",
    'sms:no_more_news' => "%s has no more news.",
    
    'sms:find_help' => "To find orgs, txt \"F [org name]\" or \"FN [location]\"",
    'sms:post_help' => 'To publish news on Envaya, txt "P [your message]".',
    'sms:language_help' => 'Current language: English. Txt "L SW" for Swahili, "L RW" for Kinyarwanda',
    'sms:login_help' => 'To log in to Envaya, txt "IN [your Envaya username] [your password]".',
    'sms:delete_help' => 'Missing id number. To delete something from Envaya, txt "DELETE [id]".',
    'sms:user_help' => "Txt \"I [user]\" to look up an org's contact info.",
    'sms:name_help' => "Txt \"NAME [your name]\" to change the name displayed when you post messages.",
    'sms:location_help' => "Txt \"LOC [your location]\" to change the location displayed when you post messages.",
    'sms:news_help' => "Txt \"N [user]\" to view latest news from org.",
    'sms:add_comment_help' => "Txt \"C [msg]\" to add a comment to this news update.",
    'sms:view_comment_help' => "Txt \"G [num]\" to view a comment on this news update. Valid num: %s",
    
    'sms:name' => "Name is currently '%s'.",
    'sms:name_changed' => "Name changed to '%s'.",
    'sms:name_not_set' => "Name not set.",
    
    'sms:location' => "Location is currently '%s'.",
    'sms:location_changed' => "Location changed to '%s'.",
    'sms:location_not_set' => "Location not set.",
    
    
    'sms:user_self_help' => 'Txt "I {username}" to see your own info.',
    
    'sms:language_changed' => 'Language changed.',
    'sms:bad_language' => 'Unknown language \'{lang}\'. Txt "L SW" for Swahili, "L RW" for Kinyarwanda',
    
    'sms:login_to_post' => 'To post your message, you need to log in to Envaya. Txt "IN [your Envaya username] [your password]"',
    'sms:post_published' => "Your news update has been published to \"N {username}\" and {url}\nIf you want to undo, txt \"DELETE {id}\"",    
    
    'sms:logged_in' => 'You are logged in as {username} ({name}). Txt "OUT" to log out.',
    
    'sms:login_success' => 'Successfully logged in.',
    
    'sms:login_unknown_user' => 'The username \'{username}\' does not exist on Envaya. Please correct the username, then txt "IN [your username] [your password]"',

    'sms:login_bad_password' => 'The password \'{password}\' was incorrect for username \'{username}\'. Please correct the password, then txt "IN [your username] [your password]".',
    'sms:publish_last_help' => 'To publish your last message ("{snippet}...") on your News page, reply with txt "P". Or, txt "HELP" for other options.',    
    
    'sms:item_not_found' => "Item {id} was not found.",
    'sms:cant_delete_item' => "You do not have access to delete this item.",
    'sms:item_deleted' => "Item deleted successfully.",
    
    'sms:logout_success' => "Successfully logged out.",        
    'sms:login_not_org' => "The username '{username}' cannot access this system because it is not registered as an Organization.",
    
    'sms:document_placeholder' => '(doc)',
    'sms:image_placeholder' => '(img)',
    
    'sms:no_add_comment_here' => "Sorry, you can't add a comment here. View a news update with 'N [user]' then try again.",
    'sms:no_view_comment_here' => "Sorry, you can't view comments here. View a news update with 'N [user]' then try again.",
    'sms:comment_not_found' => "Comment %s not found.",
    'sms:no_comments_here' => "There are no comments here yet.",
    
    'sms:comment_published' => "Your comment has been published to \"V {id}\" and {url}\nTxt \"DELETE {id}\" to undo.",
    
    'sms:length_set' => "Maximum length of SMS reply changed to %s",
);