
<?php

return function() {

    EmailSubscription_Discussion::init_for_entity(UserScope::get_root(), Config::get('admin_email'));

};