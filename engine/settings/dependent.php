<?php
    global $CONFIG;

    $CONFIG->url = "http://{$CONFIG->domain}/";
    $CONFIG->secure_url = ($CONFIG->ssl_enabled) ? "https://{$CONFIG->domain}/" : $CONFIG->url;
