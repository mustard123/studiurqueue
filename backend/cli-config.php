<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;

// replace with file to your own project bootstrap
require_once __DIR__."/bootstrap.php";
$container = setup(false, true);
$entityManager = $container->get(\Doctrine\ORM\EntityManager::class);


return ConsoleRunner::createHelperSet($entityManager);