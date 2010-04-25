<?php

$props = get_input_array("prop");
$from = get_input('from');

$area2 = array();

set_theme('editor');

foreach ($props as $propStr)
{
    $guidProp = explode('.', $propStr);
    $guid = $guidProp[0];
    $prop = $guidProp[1];    
    
    $entity = get_entity($guid);
    
    if ($entity && $entity->canEdit() && $entity->get($prop))
    {    
        $area2[] = elgg_view("translation/translate", 
            array('entity' => $entity, 'property' => $prop, 'from' => $from));
    }
}    

$title = elgg_echo("trans:translate");

$body = elgg_view_layout("one_column_padded", elgg_view_title($title), implode("<hr><br>", $area2));            

page_draw($title,$body);

?>