<?php
    // Copyright 2009 Google Inc. All Rights Reserved.
    $GA_ACCOUNT = Config::get('google_analytics_id');
    $GA_PIXEL = "/ga.php";

    $url = "";
    $url .= $GA_PIXEL . "?";
    $url .= "utmac=" . $GA_ACCOUNT;
    $url .= "&utmn=" . rand(0, 0x7fffffff);

    $referer = @$_SERVER["HTTP_REFERER"];
    $query = $_SERVER["QUERY_STRING"];
    $path = $_SERVER["REQUEST_URI"];

    if (empty($referer)) {
      $referer = "-";
    }
    $url .= "&utmr=" . urlencode($referer);

    if (!empty($path)) {
      $url .= "&utmp=" . urlencode($path);
    }

    $url .= "&guid=ON";

    echo "<img src='".escape($url)."' width='1' height='1' />";
