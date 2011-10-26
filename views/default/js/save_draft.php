<?php
    // can only have one of these per page
    if ($INCLUDE_COUNT == 0)
    {
        echo view('js/xhr');
        echo view('js/dom');
        echo "<script type='text/javascript'>";
        echo "window.save_draft_guid = ".json_encode($vars['guid']).";\n";
        echo "window.save_draft_url = ".json_encode(@$vars['url']).";\n";                
        echo get_inline_js('inline/save_draft.js');
        echo "</script>";
    }