<?php

require_once "scripts/qworker.php";
execute_queue_worker(FunctionQueue::LowPriority);
