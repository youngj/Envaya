<?php 
    $list = Input::get_string("list");                      
    echo $list ? view("org/browse_list") : view("org/browse_map");
    