<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../bootstrap.php';

$container = setup(false);

use API\Util\JwtUtil;

$klein = new \Klein\Klein();
$request = \Klein\Request::createFromGlobals();


// Grab the server-passed "REQUEST_URI"
$uri = $request->server()->get('REQUEST_URI');

define('APP_PATH', '/backend/api');
// Set the request URI to a modified one (without the "subdirectory") in it
$request->server()->set('REQUEST_URI', substr($uri, strlen(APP_PATH)));

$klein->respond(function ($request, $response, $service, $app) {

    // set content type for all responses and set CORS headers
    $response->header('Content-Type', 'application/json');
    $response->header('Access-Control-Allow-Origin','*');
    $response->header('Access-Control-Allow-Headers', '*');
    $response->header('Access-Control-Allow-Methods', '*');


    if($request->method('options')){
        $response->code(200);
        $response->send();
        die();
    }

    $jwt = $request->headers()->get('Authorization') ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];


    if (!JwtUtil::jwt_is_valid($jwt, getenv('JWT_SECRET_KEY'))) {
        $response->code(401);
        $response->body(json_encode(array("message" => "token invalid", "token" => $jwt, "REDIRECT_HTTP_AUTHORIZATION" => $_SERVER['REDIRECT_HTTP_AUTHORIZATION'])));
        $response->send();
        die();


    } else {
        $user_id = JwtUtil::get_encoded_param($jwt, getenv('JWT_SECRET_KEY'), 'id');
        $userlevel = JwtUtil::get_encoded_param($jwt, getenv('JWT_SECRET_KEY'), 'userlevel');
        $app->jwt = $jwt;
        $app->user_id = $user_id;
        $app->userlevel = $userlevel;
    }


});

/**
 * TEST route
 */
$klein->respond('GET', '/', function () {
    return 'Hello api!';
});


/**
 *  TICKET API
 */
$klein->respond('POST', '/ticket', $container->call(function (\API\Controller\TicketController $ticketController){
    return array($ticketController, 'post_ticket');
}));

$klein->respond('GET', '/tickets', $container->call(function (\API\Controller\TicketController $ticketController){
    return array($ticketController, 'get_open_tickets');
}));

$klein->respond('GET', '/tickets/user/[i:user_id]', $container->call(function (\API\Controller\TicketController $ticketController){
    return array($ticketController, 'get_user_tickets');
}));


//$klein->respond('DELETE', '/ticket/[i:ticket_id]', $container->call(function (\API\Controller\TicketController $ticketController){
//    return array($ticketController, 'delete_ticket');
//}));


$klein->respond('PUT', '/ticket/[i:ticket_id]/close', $container->call(function (\API\Controller\TicketController $ticketController){
    return array($ticketController, 'close_ticket');
}));

$klein->respond('PUT', '/ticket/[i:ticket_id]/start', $container->call(function (\API\Controller\TicketController $ticketController){
    return array($ticketController, 'start_ticket');
}));
// END TICKET API


/**
 * ADMIN API
 */
$klein->respond('PUT', '/admin-settings', $container->call(function (\API\Controller\AdminController $adminController){
    return array($adminController, 'update_admins');
}));
// END ADMIN API




$klein->dispatch($request);