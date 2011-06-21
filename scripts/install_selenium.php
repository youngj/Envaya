<?php
    /*
     * Downloads the latest version of the Selenium Server jar file into the dataroot directory.
     * Allows us to avoid storing this jar file in git, since it is very large (>20MB) and changes often.
     */
     
    require_once dirname(__DIR__)."/engine/config.php";
    Config::load();
     
    $dataroot = Config::get('dataroot');
    $selenium_jar = Config::get('selenium_jar');
    $selenium_path = "$dataroot/$selenium_jar";
    $selenium_tmp_path = "$selenium_path.tmp";    
    
    if (!is_file($selenium_path))
    {    
        $selenium_url = "http://selenium.googlecode.com/files/$selenium_jar";    
        echo "Downloading selenium from $selenium_url to $selenium_path...\n";
        
        $out = fopen($selenium_tmp_path, 'wb');
        if (!$out)
        {
            echo "Could not open $selenium_tmp_path for writing.\n";
            die;
        }
        
        $last_time = 0;
        
        $progress = function($dl_total, $dl_current, $_x, $_y) use (&$last_time)
        {
            if ($dl_total && time() - $last_time > 5)
            {
                $dl_pct = ($dl_current * 100.0 / $dl_total);
                $dl_total_k = (int)($dl_total/1024);
            
                echo sprintf("%0.2f", $dl_pct) . "% of $dl_total_k KB\n";
                $last_time = time();
            }    
            return 0;
        };
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_FILE, $out);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_NOPROGRESS, false);
        curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, $progress);
        curl_setopt($ch, CURLOPT_URL, $selenium_url);
        $res = curl_exec($ch);
        if (!$res)
        {
            echo "Error downloading file from $selenium_url\n";
            die;
        }
        curl_close($ch);
        fclose($out);        

        $res = copy($selenium_tmp_path, $selenium_path);        
        if (!$res)
        {
            die;
        }        
    }

    if (is_file($selenium_tmp_path))
    {
        unlink($selenium_tmp_path);
    }