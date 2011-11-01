<?php

return function() {

    $admin = get_or_create_user('testadmin', 'Person');
    
    $root_scope = UserScope::get_root();
    
    Permission_ManageLanguage::grant_explicit($root_scope, $admin);
    Permission_EditTranslation::grant_explicit($root_scope, $admin);
    
};