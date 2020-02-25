<?php
require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../bootstrap.php';

$container = setup(false);

$klein = new \Klein\Klein();
$request = \Klein\Request::createFromGlobals();

// Grab the server-passed "REQUEST_URI"
$uri = $request->server()->get('REQUEST_URI');

define('APP_PATH', '/backend/login');
// Set the request URI to a modified one (without the "subdirectory") in it
$request->server()->set('REQUEST_URI', substr($uri, strlen(APP_PATH)));



$klein->respond('GET', '/login-page', $container->call(function (\Login\LoginController $loginController){
    return array($loginController, 'sign_in');
}));


$klein->dispatch($request);