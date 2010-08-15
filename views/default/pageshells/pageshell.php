<?php

    /**
     * Elgg pageshell
     * The standard HTML page shell that everything else fits into
     *
     * @package Elgg
     * @subpackage Core
     * @author Curverider Ltd
     * @link http://elgg.org/
     *
     * @uses $vars['config'] The site configuration settings, imported
     * @uses $vars['title'] The page title
     * @uses $vars['body'] The main content of the page
     */

    header("Content-type: text/html; charset=UTF-8");
    echo view('page_elements/header', $vars);
    echo view('page_elements/topbar', $vars);
    echo SessionMessages::view_all();
    echo $vars['body'];
    echo view('page_elements/footer', $vars);
