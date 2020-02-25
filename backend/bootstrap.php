<?php
/**
 * Created by IntelliJ IDEA.
 * User: silas
 * Date: 03.02.20
 * Time: 17:15
 */

require_once __DIR__ . '/vendor/autoload.php';

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

function setup($use_test_db, $cli_call=false){

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    $dotenv->required('JWT_SECRET_KEY')->notEmpty();
    $dotenv->required('PRODUCTION')->isBoolean();

    if (filter_var(getenv('PRODUCTION'), FILTER_VALIDATE_BOOLEAN) && !$use_test_db) {

        $isDevMode = true;
        $proxyDir = null;
        $cache = null;
        $useSimpleAnnotationReader = false;
        $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__ . "/api/src/Model"), $isDevMode, $proxyDir, $cache, $useSimpleAnnotationReader);

    } else {
        $isDevMode = true;
        $proxyDir = null;
        $cache = null;
        $useSimpleAnnotationReader = false;
        $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__ . "/api/src/Model"), $isDevMode, $proxyDir, $cache, $useSimpleAnnotationReader);
    }

    $DB_HOST = getenv('DB_HOST');
    if ($cli_call){
        $DB_HOST = "127.0.0.1";
    }

    if (!$use_test_db){
        $connectionParams = array(
            'url' => 'mysql://' . getenv('DB_USERNAME') . ':' . getenv('DB_PASSWORD') . '@' . $DB_HOST . '/' . getenv('DATABASE')
        );
    } else {
        $connectionParams = array(
            'url' => 'mysql://' . getenv('DB_USERNAME') . ':' . getenv('DB_PASSWORD') . '@' . $DB_HOST . '/' . getenv('TEST_DATABASE')
        );
    }

    $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams);

    $entityManager = EntityManager::create($conn, $config);

    /*
     * Setup dependency injection container
     * */
    $builder = new \DI\ContainerBuilder();
    $builder->useAutowiring(false);
    //Enable annotations when testing for easy integration with php-unit
    !$use_test_db ? $builder->useAnnotations(false): $builder->useAnnotations(true);
    $builder->addDefinitions([
        \API\Repository\UserRepo::class => function (\Psr\Container\ContainerInterface $c) use ($entityManager) {
            return $entityManager->getRepository(\API\Model\User::class);
        },

        \API\Repository\TicketRepo::class=> function (\Psr\Container\ContainerInterface $c) use ($entityManager){
            return $entityManager->getRepository(\API\Model\Ticket::class);
        },

        \API\Service\TicketService::class => function (\Psr\Container\ContainerInterface $c) {
            return new \API\Service\TicketService($c->get(\API\Repository\UserRepo::class), $c->get(\API\Repository\TicketRepo::class));
        },

        \Login\LoginController::class => function(\Psr\Container\ContainerInterface $c){
            return new \Login\LoginController($c->get(\API\Repository\UserRepo::class));
        },

        \API\Service\AdminService::class => function(\Psr\Container\ContainerInterface $c){
        return new \API\Service\AdminService($c->get(\API\Repository\UserRepo::class));
        },

        \API\Controller\AdminController::class => function(\Psr\Container\ContainerInterface $c){
        return new \API\Controller\AdminController($c->get(\API\Service\AdminService::class));
        },

        \API\Controller\TicketController::class => function(\Psr\Container\ContainerInterface $c){
            return new \API\Controller\TicketController($c->get(\API\Service\TicketService::class));
        },

        EntityManager::class => function (\Psr\Container\ContainerInterface $c) use ($entityManager){
        return $entityManager;
        }
    ]);

    return $container = $builder->build();
}

