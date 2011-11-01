<?php
    require_once "start.php";      
    
    $users = User::query()
        ->where('icons_json like ?', '%http:%')
        ->filter();
        
    foreach ($users as $user)
    {
        error_log($user->username);
        $user->icons_json = str_replace("http:", "", $user->icons_json);
        $user->save();
    }
    
    $people = Person::query()->filter();
    foreach ($people as $person)
    {
        error_log($person->username);
        $person->set_defaults();
        $person->init_default_widgets();
        $person->save();
    }
    
    foreach (FeaturedSite::query()->where('image_url like ?', 'http:%')->filter() as $site)
    {
        $site->image_url = str_replace("http:", "", $site->image_url);
        $site->save();
    }
    
    foreach (FeaturedPhoto::query()->where('href like ? or image_url like ?', 'http:%', 'http:%')->filter() as $photo)
    {
        $photo->href = str_replace("http:", "", $photo->href);
        $photo->image_url = str_replace("http:", "", $photo->image_url);
        $photo->save();
    }    