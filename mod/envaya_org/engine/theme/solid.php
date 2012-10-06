<?php

class Theme_Solid extends Theme_UserSite
{
    static $css = 'topmenu4';    

    static function get_vars()
    {
        return static::merge_vars(parent::get_vars(), array(    
            'box_shadow' => array(
                'selector' => '#main_content',
                'type' => 'box_shadow',
                'default' => '',
            ),
        ));
    }
}
