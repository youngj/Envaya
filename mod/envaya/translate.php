<?php

$metadataId = get_input("metadata_id");
$metadata = get_metadata($metadataId);

if ($metadata && $metadata->value_type == 'text' && $metadata->access_id == 2) 
{   
    $entity = get_entity($metadata->entity_guid);
    
    $org = $entity->getRootContainerEntity();
    
    if ($org instanceof Organization)
    {
        $text = get_metastring($metadata->value_id);
        $area2 = elgg_view("translation/translate", array('text' => $text, 'metadata_id' => $metadataId, 'org'=>$org));
        $body = elgg_view_layout("one_column_padded", elgg_view_title(elgg_echo("trans:translate")), $area2);            
    }   
    else
    {
        $body = elgg_view("blog/notfound");
        $title = elgg_echo("trans:invalid_id");        
    }
} 
else 
{
    $body = elgg_view("blog/notfound");
    $title = elgg_echo("trans:invalid_id");
}

page_draw($title,$body);

?>