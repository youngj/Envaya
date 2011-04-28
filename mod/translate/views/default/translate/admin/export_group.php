<?php
    $group = $vars['group'];    
    
    $keys = $group->get_available_keys();
    
    echo "<?php\n\n";
    echo "return array(\n";    
    
    $items = array();
    foreach ($keys as $key)
    {
        if ($key->best_translation != '')
        {        
            echo "    ".var_export($key->name, true)." => ".var_export($key->best_translation, true).",\n";
        }
    }
    echo ");\n\n";
