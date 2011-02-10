<?php
/**
 * $Id: tiny_mce_gzip.php 315 2007-10-25 14:03:43Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright © 2005-2006, Moxiecode Systems AB, All rights reserved.
 *
 * This file compresses the TinyMCE JavaScript using GZip and
 * enables the browser to do two requests instead of one for each .js file.
 * Notice: This script defaults the button_tile_map option to true for extra performance.
 */

    // Set the error reporting to minimal.
    @error_reporting(E_ERROR | E_WARNING | E_PARSE);

    require_once(dirname(dirname(__DIR__))."/engine/settings.php");

    global $CONFIG;

    $diskCache = true;
    $compress = true;
    $suffix = "";// "_src";
    $cachePath = $CONFIG->dataroot; // this is where the .gz files will be stored
    $expiresOffset = 3600 * 24 * 60;
    $encodings = array();
    $supportsGzip = false;
    $enc = "";
    $cacheKey = "";

    header("Content-type: text/javascript");
    header("Vary: Accept-Encoding");  // Handle proxies
    header("Expires: " . gmdate("D, d M Y H:i:s", time() + $expiresOffset) . " GMT");

    if ($diskCache)
    {
        $cacheKey = md5("$suffix{$CONFIG->cache_version}");

        if ($compress)
            $cacheFile = $cachePath . "/tiny_mce_" . $cacheKey . ".gz";
        else
            $cacheFile = $cachePath . "/tiny_mce_" . $cacheKey . ".js";
    }

    // Check if it supports gzip
    if (isset($_SERVER['HTTP_ACCEPT_ENCODING']))
    {
        $encodings = explode(',', strtolower(preg_replace("/\s+/", "", $_SERVER['HTTP_ACCEPT_ENCODING'])));
    }

    if ((in_array('gzip', $encodings) || in_array('x-gzip', $encodings) || isset($_SERVER['---------------'])) && function_exists('ob_gzhandler') && !ini_get('zlib.output_compression')) {
        $enc = in_array('x-gzip', $encodings) ? "x-gzip" : "gzip";
        $supportsGzip = true;
    }

    // Use cached file disk cache
    if ($diskCache && $supportsGzip && file_exists($cacheFile)) {
        if ($compress)
            header("Content-Encoding: " . $enc);

        echo getFileContents($cacheFile);
        die();
    }

    $content =  getFileContents("tiny_mce$suffix.js").
                getFileContents("themes/advanced/editor_template$suffix.js");

    if ($supportsGzip)
    {
        if ($compress)
        {
            header("Content-Encoding: " . $enc);
            $cacheData = gzencode($content, 9, FORCE_GZIP);
        }
        else
        {
            $cacheData = $content;
        }

        if ($diskCache && $cacheKey != "")
        {
            @file_put_contents($cacheFile, $cacheData);
        }

        // Stream to client
        echo $cacheData;
    } else {
        // Stream uncompressed content
        echo $content;
    }

    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

    function getFileContents($path) {
        $path = realpath($path);

        if (!$path || !@is_file($path))
            return "";

        return @file_get_contents($path);
    }

?>