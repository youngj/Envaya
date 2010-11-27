<?php
    require_once(__DIR__."/engine/start.php");    

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

    Route::set('sub_item', '<username>/<controller>/<id>(/<action>)',
        array('controller' => '(post|report|reporting)')
    )->defaults(array(
        'action'     => 'index',
    ));

    Route::set('profile', '(<username>(/<widgetname>(/<action>)))')->defaults(array(
        'controller' => 'profile',
        'action'     => 'index',
        'widgetname' => 'home',
    ));

    if (get_input('login') && !Session::isloggedin())
    {
        force_login();
    }

    echo Request::instance()
        ->execute()
        ->send_headers()
        ->response;
