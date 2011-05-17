<?php
    // can only have one of these per page
    if ($INCLUDE_COUNT == 0)
    {
        echo view('js/xhr');
        echo view('js/dom');
        echo "window.save_draft_guid = ".json_encode($vars['guid']).";\n";
        readfile(Config::get('path').'_media/inline_js/save_draft.js');
    }