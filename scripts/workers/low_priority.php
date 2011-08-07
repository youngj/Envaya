<?php

require_once "scripts/worker.php";
execute_queue_worker(FunctionQueue::LowPriority, 1.75);
