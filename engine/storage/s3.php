<?php
    // This is a fresh rewrite of the previous S3 class using PHP 5.
    // All transfers are done using PHP's native curl extension rather
    // than piping everything to the command line as before. (That was
    // a dirty hack in hindsight.) Copying S3 objects is now supported
    // as well. If you'd like to access the previous version, you may do
    // so here: http://code.google.com/p/php-aws/source/browse/branches/original-stable/
 
    class Storage_S3 implements Storage
    {
        private $key;
        private $privateKey;
        private $host;
        private $date;
        private $curlInfo;
 
        public function __construct()
        {
			global $CONFIG;
            $this->key        = $CONFIG->s3_key;
            $this->privateKey = $CONFIG->s3_private;
            $this->host       = 's3.amazonaws.com';
            $this->date       = gmdate('D, d M Y H:i:s T');
            return true;
        }

		public function get_url($bucket_name, $s3_path)
		{	
			return "http://{$bucket_name}.s3.amazonaws.com/{$s3_path}";
		}
 
        public function list_buckets()
        {
            $request = array('verb' => 'GET', 'resource' => '/');
            $result = $this->sendRequest($request);
            $xml = simplexml_load_string($result);
 
            if($xml === false || !isset($xml->Buckets->Bucket))
                return false;
 
            $buckets = array();
            foreach($xml->Buckets->Bucket as $bucket)
                $buckets[] = (string) $bucket->Name;
            return $buckets;
        }
 
        public function create_bucket($name)
        {
            $request = array('verb' => 'PUT', 'resource' => "/$name/");
            $result = $this->sendRequest($request);
            return $this->curlInfo['http_code'] == '200';
        }
 
        public function delete_bucket($name)
        {
            $request = array('verb' => 'DELETE', 'resource' => "/$name/");
            $result = $this->sendRequest($request);
            return $this->curlInfo['http_code'] == '204';
        }
 
        public function get_bucket_location($name)
        {
            $request = array('verb' => 'GET', 'resource' => "/$name/?location");
            $result = $this->sendRequest($request);
            $xml = simplexml_load_string($result);
 
            if($xml === false)
                return false;
 
            return (string) $xml->LocationConstraint;
        }
 
        public function get_bucket_contents($name, $prefix = null, $marker = null, $delimeter = null, $max_keys = null)
        {
            $contents = array();
 
            do
            {
                $q = array();
                if(!is_null($prefix)) $q[] = 'prefix=' . $prefix;
                if(!is_null($marker)) $q[] = 'marker=' . $marker;
                if(!is_null($delimeter)) $q[] = 'delimeter=' . $delimeter;
                if(!is_null($max_keys)) $q[] = 'max-keys=' . $max_keys;
                $q = implode('&', $q);
                if(strlen($q) > 0)
                    $q = '?' . $q;
 
                $request = array('verb' => 'GET', 'resource' => "/$name/$q");
                $result = $this->sendRequest($request);
                $xml = simplexml_load_string($result);
 
                if($xml === false)
                    return false;
 
                foreach($xml->Contents as $item)
                    $contents[(string) $item->Key] = array('LastModified' => (string) $item->LastModified, 'ETag' => (string) $item->ETag, 'Size' => (string) $item->Size);
 
                $marker = (string) $xml->Marker;
            }
            while((string) $xml->IsTruncated == 'true' && is_null($max_keys));
 
            return $contents;
        }
 
        public function upload_file($bucket_name, $s3_path, $fs_path, $web_accessible = false, $headers = null)
        {
            // Some useful headers you can set manually by passing in an associative array...
            // Cache-Control
            // Content-Type
            // Content-Disposition (alternate filename to present during web download)
            // Content-Encoding
            // x-amz-meta-*
            // x-amz-acl (private, public-read, public-read-write, authenticated-read)
 
            $request = array('verb' => 'PUT',
                             'resource' => "/$bucket_name/$s3_path",
                             'content-md5' => $this->base64(md5_file($fs_path)));
 
            $fh = fopen($fs_path, 'r');
            $curl_opts = array('CURLOPT_PUT' => true,
                               'CURLOPT_INFILE' => $fh,
                               'CURLOPT_INFILESIZE' => filesize($fs_path),
                               'CURLOPT_CUSTOMREQUEST' => 'PUT');
 
            if(is_null($headers))
                $headers = array();
 
            $headers['Content-MD5'] = $request['content-md5'];
 
            if($web_accessible === true && !isset($headers['x-amz-acl']))
                $headers['x-amz-acl'] = 'public-read';
 
            if(!isset($headers['Content-Type']))
            {
                $ext = pathinfo($s3_path, PATHINFO_EXTENSION);
                $headers['Content-Type'] = isset($this->mimeTypes[$ext]) ? $this->mimeTypes[$ext] : 'application/octet-stream';
            }
            $request['content-type'] = $headers['Content-Type'];
 
            $result = $this->sendRequest($request, $headers, $curl_opts);
            fclose($fh);
            return $this->curlInfo['http_code'] == '200';
        }
 
        public function delete_object($bucket_name, $s3_path)
        {
            $request = array('verb' => 'DELETE', 'resource' => "/$bucket_name/$s3_path");
            $result = $this->sendRequest($request);
            return $this->curlInfo['http_code'] == '204';
        }
 
        public function copy_object($bucket_name, $s3_path, $dest_bucket_name, $dest_s3_path, $web_accessible = false)
        {
            $request = array('verb' => 'PUT', 'resource' => "/$dest_bucket_name/$dest_s3_path");
            $headers = array('x-amz-copy-source' => "/$bucket_name/$s3_path");
            
            if($web_accessible === true)
                $headers['x-amz-acl'] = 'public-read';            
            
            $result = $this->sendRequest($request, $headers);
 
            if($this->curlInfo['http_code'] != '200')
                return false;
 
            $xml = simplexml_load_string($result);
            if($xml === false)
                return false;
 
            return isset($xml->LastModified);
        }
 
        public function get_object_info($bucket_name, $s3_path)
        {
            $request = array('verb' => 'HEAD', 'resource' => "/$bucket_name/$s3_path");
            $curl_opts = array('CURLOPT_HEADER' => true, 'CURLOPT_NOBODY' => true);
            $result = $this->sendRequest($request, null, $curl_opts);
            $xml = @simplexml_load_string($result);
 
            if($xml !== false)
                return false;
 
            preg_match_all('/^(\S*?): (.*?)$/ms', $result, $matches);
            $info = array();
            for($i = 0; $i < count($matches[1]); $i++)
                $info[$matches[1][$i]] = $matches[2][$i];
 
            if(!isset($info['Last-Modified']))
                return false;
 
            return $info;
        }
 
        public function download_file($bucket_name, $s3_path, $fs_path)
        {
            $request = array('verb' => 'GET', 'resource' => "/$bucket_name/$s3_path");
 
            $fh = fopen($fs_path, 'w');
            $curl_opts = array('CURLOPT_FILE' => $fh);
 
            $headers = array();
 
            $result = $this->sendRequest($request, $headers, $curl_opts);
            fclose($fh);
            return $this->curlInfo['http_code'] == '200';
 
        }
 
        public function getAuthenticatedURLRelative($bucket_name, $s3_path, $seconds_till_expires = 3600)
        {
            return $this->getAuthenticatedURL($bucket_name, $s3_path, gmmktime() + $seconds_till_expires);
        }
 
        public function getAuthenticatedURL($bucket_name, $s3_path, $expires_on)
        {
            // $expires_on must be a GMT Unix timestamp
 
            $request = array('verb' => 'GET', 'resource' => "/$bucket_name/$s3_path", 'date' => $expires_on);
            $signature = urlencode($this->signature($request));
 
            $url = sprintf("http://%s.s3.amazonaws.com/%s?AWSAccessKeyId=%s&Expires=%s&Signature=%s",
                            $bucket_name,
                            $s3_path,
                            $this->key,
                            $expires_on,
                            $signature);
            return $url;
        }
 
        public function sendRequest($request, $headers = null, $curl_opts = null)
        {
            if(is_null($headers))
                $headers = array();
 
            $headers['Date'] = $this->date;
            $headers['Authorization'] = 'AWS ' . $this->key . ':' . $this->signature($request, $headers);
            foreach($headers as $k => $v)
                $headers[$k] = "$k: $v";
 
            $uri = 'http://' . $this->host . $request['resource'];
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $uri);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request['verb']);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // curl_setopt($ch, CURLOPT_VERBOSE, true);
 
            if(is_array($curl_opts))
            {
                foreach($curl_opts as $k => $v)
                    curl_setopt($ch, constant($k), $v);
            }
 
            $result = curl_exec($ch);
            $this->curlInfo = curl_getinfo($ch);
            curl_close($ch);
            return $result;
        }
 
        private function signature($request, $headers = null)
        {
            if(is_null($headers))
                $headers = array();
 
            $CanonicalizedAmzHeadersArr = array();
            $CanonicalizedAmzHeadersStr = '';
            foreach($headers as $k => $v)
            {
                $k = strtolower($k);
 
                if(substr($k, 0, 5) != 'x-amz') continue;
 
                if(isset($CanonicalizedAmzHeadersArr[$k]))
                    $CanonicalizedAmzHeadersArr[$k] .= ',' . trim($v);
                else
                    $CanonicalizedAmzHeadersArr[$k] = trim($v);
            }
            ksort($CanonicalizedAmzHeadersArr);
 
            foreach($CanonicalizedAmzHeadersArr as $k => $v)
                $CanonicalizedAmzHeadersStr .= "$k:$v\n";
 
            $str  = $request['verb'] . "\n";
            $str .= isset($request['content-md5']) ? $request['content-md5'] . "\n" : "\n";
            $str .= isset($request['content-type']) ? $request['content-type'] . "\n" : "\n";
            $str .= isset($request['date']) ? $request['date']  . "\n" : $this->date . "\n";
            $str .= $CanonicalizedAmzHeadersStr . preg_replace('/\?.*/', '', $request['resource']);
 
            $sha1 = $this->hasher($str);
            return $this->base64($sha1);
        }
 
        // Algorithm adapted (stolen) from http://pear.php.net/package/Crypt_HMAC/)
        private function hasher($data)
        {
            $key = $this->privateKey;
            if(strlen($key) > 64)
                $key = pack('H40', sha1($key));
            if(strlen($key) < 64)
                $key = str_pad($key, 64, chr(0));
            $ipad = (substr($key, 0, 64) ^ str_repeat(chr(0x36), 64));
            $opad = (substr($key, 0, 64) ^ str_repeat(chr(0x5C), 64));
            return sha1($opad . pack('H40', sha1($ipad . $data)));
        }
 
        private function base64($str)
        {
            $ret = '';
            for($i = 0; $i < strlen($str); $i += 2)
                $ret .= chr(hexdec(substr($str, $i, 2)));
            return base64_encode($ret);
        }
 
        private function match($regex, $str, $i = 0)
        {
            if(preg_match($regex, $str, $match) == 1)
                return $match[$i];
            else
                return false;
        }
		 
        private $mimeTypes = UploadedFile::$mime_types;
	}
 