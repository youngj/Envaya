<?php

define('SECTOR_OTHER', 99);

class ReportStatus
{
    const Blank = 0;
    const Draft = 4;
    const Published = 8;
}

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
            'region:tz:arusha',
            'region:tz:dar',
            'region:tz:dodoma',
            'region:tz:iringa',
            'region:tz:kagera',
            'region:tz:kigoma',
            'region:tz:kilimanjaro',
            'region:tz:lindi',
            'region:tz:manyara',
            'region:tz:mara',
            'region:tz:mbeya',
            'region:tz:morogoro',
            'region:tz:mtwara',
            'region:tz:mwanza',
            'region:tz:pemba_n',
            'region:tz:pemba_s',
            'region:tz:pwani',
            'region:tz:rukwa',
            'region:tz:ruvuma',
            'region:tz:shinyanga',
            'region:tz:singida',
            'region:tz:tabora',
            'region:tz:tanga',
            'region:tz:zanzibar_cs',
            'region:tz:zanzibar_n',
            'region:tz:zanzibar_w',
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
