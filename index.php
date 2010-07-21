<?php
    define('externalpage',true);

    require_once(__DIR__."/engine/start.php");

    spl_autoload_register('auto_load');

    if (get_input('login'))
    {
        gatekeeper();
    }

    Route::set('page', 'page/<name>')->defaults(array(
        'controller' => 'page',
        'action' => 'view',
    ));

    Route::set('default', '(<controller>(/<action>(/<id>)))',
        array('controller' => '(pg|home|org|admin|action)?')
    )->defaults(array(
            'controller' => 'home',
            'action'     => 'index',
    ));

    Route::set('post', '<username>/post/<id>(/<action>)')->defaults(array(
        'controller' => 'post',
        'action'     => 'index',
    ));

    Route::set('profile', '(<username>(/<widgetname>(/<action>)))')->defaults(array(
        'controller' => 'profile',
        'action'     => 'index',
        'widgetname' => 'home',
    ));

    echo Request::instance()
        ->execute()
        ->send_headers()
        ->response;