<?php

/* 
 * A widget that displays an organization's team members - a free-text HTML page
 * with a simple UI for adding another member.
 */
class Widget_Team extends Widget_Generic
{
    function render_edit()
    {
        return view("widgets/team_edit", array('widget' => $this));
    }

}
