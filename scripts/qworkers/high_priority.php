<?php

require_once "scripts/qworker.php";
execute_queue_worker(TaskQueue::HighPriority);
