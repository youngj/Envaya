<?php

$url = "http://".Config::get('domain')."/";

return array(
    'url' => $url,
    'secure_url' => Config::get('ssl_enabled') ? "https://".Config::get('domain')."/" : $url,
);