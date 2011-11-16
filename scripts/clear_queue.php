<?php

require_once "scripts/cmdline.php";
require_once "start.php";
FunctionQueue::clear(FunctionQueue::HighPriority);
FunctionQueue::clear(FunctionQueue::LowPriority);
