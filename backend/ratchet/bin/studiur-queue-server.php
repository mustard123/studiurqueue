<?php

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use SocketApp\StudQ;

require dirname(__DIR__) . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../../");
$dotenv->load();

$dotenv->required('JWT_SECRET_KEY')->notEmpty();
$dotenv->required('PRODUCTION')->isBoolean();


$loop = React\EventLoop\Factory::create();
$studq = new \SocketApp\StudQ();

$context = new React\ZMQ\Context($loop);
$pull = $context->getSocket(ZMQ::SOCKET_PULL);
$pull->bind('tcp://127.0.0.1:5555');
$pull->on('message', array($studq, 'onTicketEntry'));

$webSock = new React\Socket\Server('0.0.0.0:7777', $loop);
$webServer = new Ratchet\Server\IoServer(
    new Ratchet\Http\HttpServer(
        new Ratchet\WebSocket\WsServer(
           $studq
        )

    ), $webSock
);


$loop->run();