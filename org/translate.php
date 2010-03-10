<?php

$body = elgg_view("blog/notfound");
$title = elgg_echo("trans:invalid_id");        

$guid = get_input("guid");
$entity = get_entity($guid);

if ($entity)
{
    $prop = get_input("property");
    $text = $entity->get($prop);
    
    if ($text) 
    {   
        $org = $entity->getRootContainerEntity();

        if ($org instanceof Organization)
        {
            $area2 = elgg_view("translation/translate", array('text' => $text, 'entity' => $entity, 'property' => $prop, 'org' => $org));
            $body = elgg_view_layout("one_column_padded", elgg_view_title(elgg_echo("trans:translate")), $area2);            
        }   
    } 
}    

page_draw($title,$body);

?>