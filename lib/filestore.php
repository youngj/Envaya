<?php

    function get_storage()
    {
        $storage_backend = Config::get('storage_backend');
        return new $storage_backend();
    }

    function get_scribd()
    {
        require_once Config::get('root')."/vendors/scribd.php";
        return new Scribd(Config::get('scribd_key'), Config::get('scribd_private'));    
    }
