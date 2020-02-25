<?php
// create_product.php <name>
require_once  __DIR__."/bootstrap.php";

$container = setup(false);

$adminService = $container->get(\API\Service\AdminService::class);


$user = new \API\Model\User("ahah", "hhh", 1);
$ticket = new \API\Model\Ticket(new DateTime(), "open", $user);
$stdq = new \API\Model\StudQPushNotification("Header", $ticket);

echo \API\Util\SerializerHelper::getJson($stdq, \API\Model\StudQPushNotification::class);