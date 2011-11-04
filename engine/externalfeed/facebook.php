<?php

/* 
 * Represents a Facebook page. Extracts statuses using RSS and
 * creates FacebookPost widgets for each imported status.
 */
class ExternalFeed_Facebook extends ExternalFeed_RSS
{
    function get_widget_class()
    {
        return 'Widget_FacebookPost';
    }
}