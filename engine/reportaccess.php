<?php

/*
 * 
 */
class ReportAccess
{
    const OpenToPublic = 0;
    const Confidential = 1;
    
    static function get_options()
    {
        return array(
            static::OpenToPublic => __('report:open_to_public'),
            static::Confidential => __('report:confidential'),
        );
    }
   
}