<?php

    /**
     * Elgg administration user main screen
     *
     * @package Elgg
     * @subpackage Core
     * @author Curverider Ltd
     * @link http://elgg.org/
     */

    // Description of what's going on
        echo "<span class=\"contentIntro\">" . view('output/longtext', array('value' => __("admin:user:description"))) . "</span>";

        echo view("admin/user_opt/adduser");

        echo view("admin/user_opt/search");

        if ($vars['list']) echo $vars['list'];

?>