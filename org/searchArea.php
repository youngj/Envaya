<?php

    header('Content-type; text/javascript');
    set_context('search');

    $latMin = get_input('latMin');
    $latMax = get_input('latMax');
    $longMin = get_input('longMin');
    $longMax = get_input('longMax');
    $sector = get_input('sector');
                
    $orgs = Organization::filterByArea(array($latMin, $longMin, $latMax, $longMax), $sector, $limit = 100);
    
    $res = array();
    foreach ($orgs as $org)
    {
        $res[] = $org->jsProperties();
    }
    
    echo "searchAreaCallback(";
    echo json_encode($res);
    echo ");";