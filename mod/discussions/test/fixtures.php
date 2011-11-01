<?php

return function() {

    $root_scope = UserScope::get_root();

    EmailSubscription_Discussion::init_for_entity($root_scope, Config::get('admin_email'));

    $admin = get_or_create_user('testadmin', 'Person');
    
    Permission_EditDiscussionMessage::grant_explicit($root_scope, $admin);

};