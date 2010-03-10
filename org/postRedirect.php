<?php

$blogPostId = get_input("blogpost");
$blogPost = get_entity($blogPostId);

$delta = get_input("delta");

$op = ($delta > 0) ? ">" : "<";
$order = ($delta > 0) ? "asc" : "desc";

$selectWhere = "SELECT * from entities WHERE type='object' AND subtype=? AND container_guid=?";

$entity = entity_row_to_elggstar(get_data_row("$selectWhere AND guid $op ? ORDER BY guid $order LIMIT 1", 
    array(T_blog, $blogPost->container_guid, $blogPost->guid)
));
if ($entity)
{
    forward($entity->getURL());
}

$entity = entity_row_to_elggstar(get_data_row("$selectWhere ORDER BY guid $order LIMIT 1", 
    array(T_blog, $blogPost->container_guid)
));
if ($entity)
{
    forward($entity->getURL());
}
