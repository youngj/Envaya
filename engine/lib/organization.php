<?php

define('SECTOR_OTHER', 99);

class DataType
{
    const Image = 2;
    const HTML = 4;
}

function regions_in_country($country)
{
    if ($country == 'tz'  || true)
    {
        $ids = array(
            'tz:arusha',
            'tz:dar',
            'tz:dodoma',
            'tz:iringa',
            'tz:kagera',
            'tz:kigoma',
            'tz:kilimanjaro',
            'tz:lindi',
            'tz:manyara',
            'tz:mara',
            'tz:mbeya',
            'tz:morogoro',
            'tz:mtwara',
            'tz:mwanza',
            'tz:pemba_n',
            'tz:pemba_s',
            'tz:pwani',
            'tz:rukwa',
            'tz:ruvuma',
            'tz:shinyanga',
            'tz:singida',
            'tz:tabora',
            'tz:tanga',
            'tz:zanzibar_cs',
            'tz:zanzibar_n',
            'tz:zanzibar_w',
        );
    }
    else
    {
        $ids = array();
    }

    $res = array();
    foreach ($ids as $id)
    {
        $res[$id] = __($id);
    }
    asort($res);
    return $res;
}

function get_static_map_url($lat, $long, $zoom, $width, $height)
{
    global $CONFIG;
    $apiKey = $CONFIG->google_api_key;
    return "http://maps.google.com/maps/api/staticmap?center=$lat,$long&zoom=$zoom&size={$width}x$height&maptype=roadmap&markers=$lat,$long&sensor=false&key=$apiKey";
}

$THEME = null;

function get_theme()
{
    global $THEME;
    return $THEME ?: 'simple';
}

function set_theme($theme)
{
    global $THEME;
    $THEME = $theme;
}

function get_themes()
{
    return array('green','brick','craft4','craft1','cotton2','wovengrass','beads','red');
}

function get_notification_frequencies()
{
    return array(
        14 => __('freq:2weeks'),
        30 => __('freq:month'),
        60 => __('freq:2months'),
        0 => __('freq:never')
    );
}
