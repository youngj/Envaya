<?php

class FeaturedSite extends Entity
{
    static $subtype_id = T_featured_site;
    static $table_name = 'featured_sites';

    static $table_attributes = array(
        'user_guid' => 0,
        'content' => '',
        'image_url' => '',
        'data_types' => 0,
        'language' => '',
        'active' => 0,
    );
}