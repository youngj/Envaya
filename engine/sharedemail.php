<?php

/* 
 * A model created when a user 'shares' a page on Envaya with someone else by email.
 */
class SharedEmail extends Model
{
    static $table_name = 'shared_emails';
    static $table_attributes = array(
        'user_guid' => 0,
        'time_shared' => 0,
        'email' => '',
        'url' => '',
    );
}

