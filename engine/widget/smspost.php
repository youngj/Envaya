<?php

/*
 * Represents a feed item imported from an external feed using ExternalFeed.
 */
class Widget_SMSPost extends Widget_Post
{
    function get_date_view()
    {
        return 'widgets/smspost_view_date';
    }
}
