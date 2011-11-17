<?php

/*
 * Installs test data required for selenium tests. Recommended for development computers,
 * not to be used on production servers.
 */

require_once __DIR__."/test_fixtures.php";

if (Config::get('debug') || Config::get('db:name') == 'envaya_test')
{
    install_test_fixtures();
    error_log("done!");
}
else
{
    error_log("install_test_data.php is not allowed when 'debug' setting is false");
}
