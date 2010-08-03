<?php

abstract class WidgetHandler
{
    abstract function view($widget);
    abstract function edit($widget);
    abstract function save($widget);
}
