<?php

$blogPostId = get_input("blogpost");
$blogPost = get_entity($blogPostId);

$delta = get_input("delta");


$op = ($delta > 0) ? ">" : "<";
$order = ($delta > 0) ? "asc" : "desc";

$subtypeId = get_subtype_id('object', 'blog');

$selectWhere = "SELECT * from {$CONFIG->dbprefix}entities 
                WHERE type='object' AND subtype=$subtypeId 
                AND container_guid={$blogPost->container_guid}";

$dt = get_data("$selectWhere AND guid $op {$blogPost->guid} ORDER BY guid $order LIMIT 1", "entity_row_to_elggstar");
                
if (!empty($dt))
{
    forward($dt[0]->getURL());
}

$dt = get_data("$selectWhere ORDER BY guid $order LIMIT 1", "entity_row_to_elggstar");
if (!empty($dt))
{
    forward($dt[0]->getURL());
}
