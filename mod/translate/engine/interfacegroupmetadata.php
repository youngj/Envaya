<?php

/*
 */
class InterfaceGroupMetadata extends Model
{
    static $table_name = 'interface_group_metadata';
    static $table_attributes = array(
        'name' => '',
        'description' => '',
        'default_status' => 1,
    );         
}