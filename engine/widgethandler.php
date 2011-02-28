<?php

/*
 * Interface for viewing/editing a specific type of Widget;
 * see subclasses defined in widgethandler/.
 */
abstract class WidgetHandler
{
    abstract function view($widget);
    abstract function edit($widget);
    abstract function save($widget);
}
