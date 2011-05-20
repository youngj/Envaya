<?php

class ExternalFeed_Twitter extends ExternalFeed_RSS
{
    function get_widget_subclass()
    {
        return 'Tweet';
    }
}