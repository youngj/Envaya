<?php

/* 
 * An interface for storing and retrieving uploaded files;
 * see implementations in storage/ directory.
 */
abstract class Storage
{
    abstract function get_url($key);
    abstract function upload_file($key, $fs_path, $web_accessible = false, $mime = null);
    abstract function delete_object($key);
    abstract function copy_object($key, $dest_key, $web_accessible = false);
    abstract function get_object_info($key);
    abstract function download_file($key, $fs_path);
    
    static function get_instance()
    {
        $storage_backend = Config::get('storage:backend');
        return new $storage_backend();
    }
    
    static function get_scribd()
    {
        require_once Engine::$root."/vendors/scribd.php";
        return new Scribd(Config::get('storage:scribd_key'), Config::get('storage:scribd_private'));    
    }
}