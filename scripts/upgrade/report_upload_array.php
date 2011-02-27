<?php

require_once "scripts/cmdline.php";
require_once "engine/start.php";

$fields = ReportField::query()->where("name IN (?,?,?) AND VALUE LIKE '{%'", 
                'success_story_1', 'success_story_2', 'success_story_3')->filter();
                
foreach ($fields as $field)
{
    $assoc_value = json_decode($field->value, true);
    
    $array_value = array();
    foreach ($assoc_value as $k => $v)
    {
        $array_value[] = $v;
    }
    
    $field->value = json_encode($array_value);
    $field->save();
}