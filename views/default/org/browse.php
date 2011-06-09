<?php 
    $list = get_input("list");                      
    echo $list ? view("org/browse_list") : view("org/browse_map");
    