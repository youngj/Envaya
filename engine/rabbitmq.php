<?php

class RabbitMQ
{
    private static $connection = null;    

    static function load_lib()
    {
        require_once Engine::$root.'/vendors/php-amqplib/amqp.inc';
    }

    static function connect()
    {        
        if (!static::$connection)
        {        
            static::load_lib();
            
            $connection = new AMQPConnection(
                Config::get('amqp:host'), 
                Config::get('amqp:port'),
                Config::get('amqp:user'),
                Config::get('amqp:password'),
                Config::get('amqp:vhost')
            );            
            
            static::$connection = $connection;
        }
        return static::$connection;
    }
    
    static function do_management_request($url, $post_data = null, $custom_request = null)
    {
        $curl = curl_init();
        
        // todo keep-alive
        
        $api_url = Config::get('rabbitmq:management_api_url');
        $username = Config::get('amqp:user');
        $password = Config::get('amqp:password');
                
        curl_setopt($curl, CURLOPT_URL, "{$api_url}{$url}");
        curl_setopt($curl, CURLOPT_USERPWD, "{$username}:{$password}");  
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false); 
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
        ));
    
        if ($custom_request)
        {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $custom_request);        
        }
    
        if ($post_data !== null)
        {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post_data));        
        }
                
        $json = curl_exec($curl);    
        if ($err = curl_error($curl)) 
        { 
            throw new IOException("Queue management error: $err");
        }    
        
        $info = curl_getinfo($curl);    
        
        curl_close($curl);    
        
        $http_code = $info['http_code'];
        
        if ($http_code >= 300 || $http_code < 200)
        {
            throw new IOException("Queue management error: HTTP $http_code");
        }
        
        $res = json_decode($json, true);
        
        return $res;
    }
}