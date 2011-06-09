<?php

/*
 * Allows retrieving an arbitrary HTTP resource via an untrusted URL.
 */
class Web_Request
{
    public $url;
    const MaxRedirects = 2;
    
    function __construct($url)
    {
        $this->url = $url;
    }
    
    static function validate_url($url)
    {
        if (!static::is_valid_url($url))
        {
            throw new ValidationException(
                strtr(__('web:invalid_url'), array('{url}' => $url))."\n".
                __('web:try_again')
            );
        }          
    }

    static function is_valid_url($url)
    {
        $parsed = parse_url($url);        
        
        $host = @$parsed['host'];
        $scheme = @$parsed['scheme'];
        
        // don't let people make us fetch suspicious URLs.
        // (they could possibly point to our own servers, and the request would be made behind the firewall)
        // only allow http or https schemes on standard ports, with no username/password or IP addresses.
               
        if (strlen($url) > 255
            || preg_match('/[<>\'"]/', $url)
            || isset($parsed['port'])      
            || isset($parsed['user']) 
            || isset($parsed['pass']) 
            || !$host
            || ($scheme != 'http' && $scheme != 'https')
            || !preg_match('/^([a-z0-9]([a-z0-9\-]*[a-z0-9])?\.)+([a-z0-9][a-z0-9\-]*[a-z0-9])$/i', $host) 
                    // valid hostnames that have at least one dot (not local network hostnames)
            || preg_match('/(\]|\d|arpa|localhost)$/i', $host)     // ip addresses
            )
        {
            return false;
        }
        
        return true;
    }        
    
    function get_response()
    {
        $cur_url = $this->url;    
    
        for ($redirect = 0; $redirect <= static::MaxRedirects; $redirect++)
        {    
            // Location header from HTTP redirect could point to unsafe URL; need to check each one
            static::validate_url($cur_url);
        
            $ch = curl_init();
            
            curl_setopt($ch, CURLOPT_URL, $cur_url);
            curl_setopt($ch, CURLOPT_REFERER, abs_url('/'));
            
            // put magic words in the user agent string so they give us the version normal people see
            curl_setopt($ch, CURLOPT_USERAGENT, 
                "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3 EnvayaBot/0.1");
            curl_setopt($ch, CURLOPT_TIMEOUT, 6);            
                        
            // sadly curl doesn't parse http headers for us
            $headers = array();
            curl_setopt($ch, CURLOPT_HEADERFUNCTION, function($curl, $line) use (&$headers) {
                if (preg_match('#([^\:]+):\s+(.*)\r\n#', $line, $match))
                {
                    $headers[$match[1]] = $match[2];
                }
                return strlen($line);            
            });
            
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $content = curl_exec($ch);                   
            $info = curl_getinfo($ch);    
                
            curl_close($ch);           
            
            if (in_array($info['http_code'], array(301,302)) && isset($headers['Location']))
            {            
                $cur_url = $headers['Location'];
            }     
            else
            {
                break;
            }
        }    
        
        $res = new Web_Response();
        $res->url = $cur_url;
        $res->status = $info['http_code'];
        $res->headers = $headers;
        $res->content = $content;
        return $res;
    }
}