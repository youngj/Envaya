<?php

class Map_Bucketizer
{
    public $lat_min;
    public $lat_max;
    public $long_min;
    public $long_max;
    public $px_width;
    public $px_height;
    
    const BucketPixels = 20.0;

    function __construct($params)
    {
        foreach ($params as $name => $value)
        {
            $this->$name = $value;
        }
    }
    
    function get_buckets($items)
    {
        $buckets = array();
        
        $d_lat = abs($this->lat_max - $this->lat_min);
        $d_long = abs($this->long_max - $this->long_min);
        
        // assumes that latitude/longitude lines are roughly uniform within viewport
        $bucket_d_lat = static::BucketPixels * $d_lat / $this->px_height;
        $bucket_d_long = static::BucketPixels * $d_long / $this->px_width;

        foreach ($items as $item)
        {
            $tid = (int)$item->tid;
            $bucket_lat_index = round($item->latitude / $bucket_d_lat);
            $bucket_long_index = round($item->longitude / $bucket_d_long);
                        
            $bucket_key = "$bucket_lat_index,$bucket_long_index";
            
            if (!isset($buckets[$bucket_key]))
            {
                $buckets[$bucket_key] = array(
                    $bucket_lat_index * $bucket_d_lat,      // center latitude of bucket
                    $bucket_long_index * $bucket_d_long,    // center longitude of bucket
                    array($tid)                            // list of guids in bucket
                );
            }
            else
            {
                $buckets[$bucket_key][2][] = $tid;
            }
        }            
        return array_values($buckets);
    }
}