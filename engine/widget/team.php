<?php
class Widget_Team extends Widget_Generic
{
    function render_edit()
    {
        return view("widgets/team_edit", array('widget' => $this));
    }

}
