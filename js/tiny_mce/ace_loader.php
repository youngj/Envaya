<?php

$root = dirname(dirname(__DIR__));

readfile("$root/_media/ace/ace.js");
echo "\n";
readfile("$root/_media/ace/mode-html.js");
echo "\n";
?>

window.onAceLoaded();
