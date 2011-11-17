<?php

/*
 * File that can be included by any PHP script to initialize the Envaya engine.
 *
 * Typically, PHP files wishing to use the Envaya engine should include this file
 * and not any files in engine/, which will be auto-loaded when needed.
 */
  
include __DIR__."/engine/engine.php";

Engine::init();
