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
    $apiKey = Config::get('google_api_key');
    return "http://maps.google.com/maps/api/staticmap?center=$lat,$long&zoom=$zoom&size={$width}x$height&maptype=roadmap&markers=$lat,$long&sensor=false&key=$apiKey";
}