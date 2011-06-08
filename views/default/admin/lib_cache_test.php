<?php
    if (Engine::$used_lib_cache) 
    {
        $lib_cache = include(Engine::get_real_path("build/lib_cache.php"));
        $size = sizeof($lib_cache);
    
        echo "<div>The lib cache is enabled. (size=$size)</div>";
    }
    else
    {
        echo "<div>The lib cache is NOT enabled.</div>";
    }
