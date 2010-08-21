<?php

    function url_with_param($url, $param, $value)
    {
        $url = parse_url($url);
        parse_str(@$url['query'],$query);
        $query[$param] = $value;

        $prefix = @$url['scheme'] ? $url['scheme']."://".$url['host'] : '';

        return $prefix.$url['path']."?".http_build_query($query);
    }

    function escape($val)
    {
        return htmlentities($val, ENT_QUOTES, 'UTF-8');
    }

    function is_pear_error($res)
    {
        return is_a($res, 'PEAR_Error');
    }

    function forward_to_referrer()
    {
        forward($_SERVER['HTTP_REFERER']);
    }

    function not_found()
    {
        $title = __('page:notfound');
        $body = view_layout('one_column_padded', view_title($title), __('page:notfound:details')."<br/><br/><br/>");
        header("HTTP/1.1 404 Not Found");
        echo page_draw($title, $body);
        exit;
    }

    function yes_no_options()
    {
        return array(
            'yes' => __('yes'),
            'no' => __('no'),
        );
    }

    function get_first_key($arr)
    {
        reset($arr);
        $pair = each($arr);
        $res = $pair[0];
        reset($arr);
        return $res;
    }    
    
    /**
     * Adds messages to the session so they'll be carried over, and forwards the browser.
     * Returns false if headers have already been sent and the browser cannot be moved.
     *
     * @param string $location URL to forward to browser to
     * @return nothing|false
     */

    function forward($location = "/")
    {
        global $CONFIG;
        if (!headers_sent())
        {
            if ($location && $location[0] == '/')
            {
                $location = substr($location, 1);
            }

            if ((substr_count($location, 'http://') == 0) && (substr_count($location, 'https://') == 0))
            {
                $location = $CONFIG->url . $location;
            }

            SessionMessages::save();

            header("Location: {$location}");
            exit;
        }
        return false;
    }

    function rewrite_to_current_domain($url)
    {
        return Request::instance()->rewrite_to_current_domain($url);
    }

    /**
     * Adds an item to the submenu
     *
     * @param string $label The human-readable label
     * @param string $link The URL of the submenu item
     */
    function add_submenu_item($label, $link, $group = 'topnav') {

        global $CONFIG;
        if (!isset($CONFIG->submenu)) $CONFIG->submenu = array();
        if (!isset($CONFIG->submenu[$group])) $CONFIG->submenu[$group] = array();
        $item = new stdClass;
        $item->value = $link;
        $item->name = $label;
        $CONFIG->submenu[$group][] = $item;

    }

    function get_submenu_group($groupname, $itemTemplate = 'canvas_header/submenu_template', $groupTemplate = 'canvas_header/submenu_group')
    {
        global $CONFIG;
        if (!isset($CONFIG->submenu))
        {
            return '';
        }

        $submenu_register = $CONFIG->submenu;
        if (!isset($submenu_register[$groupname]))
        {
            return '';
        }

        $submenu = array();
        $submenu_register_group = $CONFIG->submenu[$groupname];

        $parsedUrl = parse_url($_SERVER['REQUEST_URI']);

        foreach($submenu_register_group as $key => $item)
        {
            $selected = endswith($item->value, $parsedUrl['path']);

            $submenu[] = view($itemTemplate,
                array(
                        'href' => $item->value,
                        'label' => $item->name,
                        'selected' => $selected,
                    ));
        }

        return view($groupTemplate, array(
            'submenu' => $submenu,
            'group_name' => $groupname
        ));
    }

    /**
     * Displays a UNIX timestamp in a friendly way (eg "less than a minute ago")
     *
     * @param int $time A UNIX epoch timestamp
     * @return string The friendly time
     */
    function friendly_time($time) {

        $diff = time() - ((int) $time);
        if ($diff < 60) {
            return __("friendlytime:justnow");
        } else if ($diff < 3600) {
            $diff = round($diff / 60);
            if ($diff == 0) $diff = 1;
            if ($diff > 1)
                return sprintf(__("friendlytime:minutes"),$diff);
            return sprintf(__("friendlytime:minutes:singular"),$diff);
        } else if ($diff < 86400) {
            $diff = round($diff / 3600);
            if ($diff == 0) $diff = 1;
            if ($diff > 1)
                return sprintf(__("friendlytime:hours"),$diff);
            return sprintf(__("friendlytime:hours:singular"),$diff);
        } else if ($diff < 604800) {
            $diff = round($diff / 86400);
            if ($diff == 0) $diff = 1;
            if ($diff > 1)
                return sprintf(__("friendlytime:days"),$diff);
            return sprintf(__("friendlytime:days:singular"),$diff);
        } else {
            $date = getdate($time);
            $now = getdate();

            $month = __("date:month:{$date['mon']}");
            $dateText = sprintf(__("date:withmonth"), $month, $date['mday']);

            if ($now['year'] != $date['year'])
            {
                return sprintf(__("date:withyear"), $dateText, $date['year']);
            }
            else
            {
                return $dateText;
            }
        }
    }
    
    function trigger_event($event, $object_type, $object = null)
    {
        return EventRegister::trigger_event($event, $object_type, $object);
    }    
    
