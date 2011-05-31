<?php

class Sphinx
{
    static function load_lib()
    {
        require_once Config::get('root')."/vendors/sphinxapi.php";
    }
    
    static function get_client()
    {
        static::load_lib();
        
        $s = new SphinxClient();
        $s->setServer(Config::get('sphinx_host'), Config::get('sphinx_port'));
        
        return $s;
    }    
    
    static function is_server_available()
    {
        $client = static::get_client();        
        $status = $client->Status();
        return ($status != false);
    }
    
    static function reindex()
    {
        FunctionQueue::queue_call(array('Sphinx', '_reindex'), array());
    }
    
    static function _reindex()
    {
        $bin_dir = Config::get('sphinx_bin_dir');
        $conf_dir = Config::get('sphinx_conf_dir');
        $pid_dir = Config::get('sphinx_pid_dir');
        
        $rotate = is_file("$pid_dir/searchd.pid") ? "--rotate" : "";
        
        system(escapeshellcmd("$bin_dir/indexer") . " --all $rotate --config " . escapeshellarg("$conf_dir/sphinx.conf"));
    }
}