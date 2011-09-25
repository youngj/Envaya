<?php

return array(
    'sms:help' => "Envaya SMS. Commands:\nP=publish news\nL=set language\nIN=log in\nOUT=log out",
    
    'sms:bad_command' => 'Unknown command for Envaya SMS. Txt "HELP" for a list of commands.',

    'sms:post_help' => 'To publish a message to your News page on Envaya, txt "P [your message]".',
    'sms:language_help' => 'Current language: English. Txt "L SW" for Swahili, "L RW" for Kinyarwanda',
    'sms:login_help' => 'To log in to Envaya, txt "IN [your Envaya username] [your password]".',
    'sms:delete_help' => 'Missing id number. To delete something from Envaya, txt "D [id]".',
    
    'sms:language_changed' => 'Language changed.',
    'sms:bad_language' => 'Unknown language \'{lang}\'. Txt "L SW" for Swahili, "L RW" for Kinyarwanda',
    
    'sms:login_to_post' => 'To post your message, you need to log in to Envaya. Txt "IN [your Envaya username] [your password]"',
    'sms:post_published' => 'Your news update has been published at {url} ! If you want to undo, txt "D {id}"',
    
    'sms:logged_out' => 'You are logged out. To log in, txt "IN [your Envaya username] [your password]"',
    'sms:logged_in' => 'You are logged in as {username} ({name}). Txt "OUT" to log out.',
    
    'sms:login_success' => 'Successfully logged in.',
    
    'sms:login_unknown_user' => 'The username \'{username}\' does not exist on Envaya. Please correct the username, then txt "IN [your username] [your password]"',

    'sms:login_bad_password' => 'The password \'{password}\' was incorrect for username \'{username}\'. Please correct the password, then txt "IN [your username] [your password]".',
    'sms:publish_last_help' => 'To publish your last message ("{snippet}...") on your News page, reply with txt "P". Or, txt "HELP" for other options.',    
    
    'sms:post_not_found' => "News update {id} was not found.",
    'sms:cant_delete_post' => "You do not have access to delete this news update.",
    'sms:post_deleted' => "News update deleted successfully.",
    
    'sms:logout_success' => "Successfully logged out.",        
    'sms:login_not_org' => "The username '{username}' cannot access this system because it is not registered as an Organization.",
);