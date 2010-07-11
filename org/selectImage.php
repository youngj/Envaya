<?php
    set_input("__topbar",0);

    $file = get_file_from_url(get_input('src'));

    $content = elgg_view('org/selectImage',
        array(
            'current' => $file,
            'position' => get_input('pos'),
            'frameId' => get_input('frameId'),
        )
    );
    page_draw('',$content);
?>
