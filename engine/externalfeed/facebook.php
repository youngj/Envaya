<?php

class ExternalFeed_Facebook extends ExternalFeed_RSS
{
    function get_widget_subclass()
    {
        return 'FacebookPost';
    }
}