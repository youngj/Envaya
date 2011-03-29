<?php
// can only have one of these per page
echo "window.save_draft_guid = ".json_encode($vars['guid']).";\n";

readfile(Config::get('path').'_media/inline_js/save_draft.js');