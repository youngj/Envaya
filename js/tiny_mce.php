<?php

$src_dir = __DIR__."/src/tiny_mce";

readfile("$src_dir/tiny_mce_src.js");
readfile("$src_dir/themes/advanced/editor_template_src.js");
readfile("$src_dir/plugins/paste/editor_plugin_src.js");
readfile("$src_dir/custom.js");