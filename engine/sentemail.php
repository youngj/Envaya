<?php

class SentEmail extends Model
{
    static $table_name = 'sent_emails';
    static $table_attributes = array(
        'email_guid' => 0,
        'user_guid' => 0,
        'time_sent' => 0,
    );       
}