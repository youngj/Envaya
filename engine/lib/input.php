<?php
    /**
     * Parameter input functions.
     * This file contains functions for getting input from get/post variables.
     *
     * @package Elgg
     * @subpackage Core

     * @author Curverider Ltd <info@elgg.com>

     * @link http://elgg.org/
     */

    /**
     * Get some input from variables passed on the GET or POST line.
     *
     * @param $variable string The variable we want to return.
     * @param $default mixed A default value for the variable if it is not found.
     */
    function get_input($variable, $default = "")
    {

        global $CONFIG;

        if (isset($CONFIG->input[$variable])) {
            $var = $CONFIG->input[$variable];

            return $var;
        }

        if (isset($_REQUEST[$variable])) {

            if (is_array($_REQUEST[$variable])) {
                $var = $_REQUEST[$variable];
            } else {
                $var = trim($_REQUEST[$variable]);
            }
            return $var;

        }

        return $default;

    }

    function get_input_array($variable)
    {
        $res = get_input($variable);
        if (is_array($res))
        {
            return $res;
        }
        else if ($res != null)
        {
            return array($res);
        }
        else
        {
            return array();
        }
    }

    /**
     * Sets an input value that may later be retrieved by get_input
     *
     * @param string $variable The name of the variable
     * @param string $value The value of the variable
     */
    function set_input($variable, $value) {

        global $CONFIG;
        if (!isset($CONFIG->input))
            $CONFIG->input = array();

        if (is_array($value))
        {
            foreach ($value as $key => $val)
                $value[$key] = trim($val);

            $CONFIG->input[trim($variable)] = $value;
        }
        else
            $CONFIG->input[trim($variable)] = trim($value);

    }

    /**
     * Takes a string and turns any URLs into formatted links
     *
     * @param string $text The input string
     * @return string The output stirng with formatted links
     **/
    function parse_urls($text) {

        return preg_replace_callback('/(?<!=["\'])((ht|f)tps?:\/\/[^\s\r\n\t<>"\'\!\(\)]+)/i',
        create_function(
            '$matches',
            '
                $url = $matches[1];
                $urltext = str_replace("/", "/<wbr />", $url);
                return "<a href=\"$url\" style=\"text-decoration:underline;\">$urltext</a>";
            '
        ), $text);
    }

    function autop($p, $br = 1)
    {
        $p = str_replace(array("\r\n", "\r"), "\n", $p); // cross-platform newlines
        $p = preg_replace("/\n\n+/", "\n\n", $p); // take care of duplicates
        $p = trim($p);
        $p = preg_replace("/\n/", "<br />", $p);
        return $p;
    }