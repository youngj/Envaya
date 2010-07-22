<?php
    require_once(__DIR__."/engine/start.php");

    if (get_input('login') && !isloggedin())
    {
        force_login();
    }

    // work around flash uploader cookie bug
    if (@$_POST['session_id'])
    {
        $_COOKIE['envaya'] = $_POST['session_id'];
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