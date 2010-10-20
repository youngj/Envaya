<?php

    function url_with_param($url, $param, $value)
    {
        $url = parse_url($url);
        parse_str(@$url['query'],$query);
        $query[$param] = $value;

        $prefix = @$url['scheme'] ? $url['scheme']."://".$url['host'] : '';

        return $prefix.$url['path']."?".http_build_query($query);
    }
    
    function is_mobile_browser()
    {
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        
        if (preg_match('/android|avantgo|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
        
    function set_persistent_cookie($name, $val)
    {
        global $CONFIG;

        $expireTime = time() + 60 * 60 * 24 * 365 * 15;
        
        if ($CONFIG->cookie_domain)
        {
            setcookie($name, $val, $expireTime, '/', $CONFIG->cookie_domain);
        }
        setcookie($name, $val, $expireTime, '/');
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
            return get_date_text($time);
        }
    }
    
    function get_date_text($time, $always_show_year = false)
    {
        if (!$time)
        {   
            return '';
        }
        $date = getdate($time);
        $now = getdate();

        $month = __("date:month:{$date['mon']}");
        $dateText = sprintf(__("date:withmonth"), $month, $date['mday']);

        if ($always_show_year || $now['year'] != $date['year'])
        {
            return sprintf(__("date:withyear"), $dateText, $date['year']);
        }
        else
        {
            return $dateText;
        }   
    }
    
    
    function trigger_event($event, $object_type, $object = null)
    {
        return EventRegister::trigger_event($event, $object_type, $object);
    }    
    
