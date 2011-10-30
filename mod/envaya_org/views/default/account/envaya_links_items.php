<?php
    if (Permission_EditMainSite::has_for_root())
    {
        echo implode("<div class='icon_separator'></div>",
            array(
                view('account/link_item', array(
                    'href' => '/org/featured', 
                    'text' => 'Featured Organizations',
                    'class' => 'icon_admin'
                )),
                view('account/link_item', array(
                    'href' => '/admin/envaya/featured_photos', 
                    'text' => 'Featured Photos',
                    'class' => 'icon_admin'
                )),
            )
        );
    }
        