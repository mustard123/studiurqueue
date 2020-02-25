<?php
/**
 * Created by IntelliJ IDEA.
 * User: silas
 * Date: 24.02.20
 * Time: 15:24
 */

require_once __DIR__."/../../bootstrap.php";
use PHPUnit\Framework\TestCase;

class AdminServiceTest extends TestCase
{

    /**
     * @\DI\Annotation\Inject()
     * @var \API\Service\AdminService
     */
    private $adminService;

    /**
     * @\DI\Annotation\Inject()
     * @var \API\Repository\UserRepo
     */
    private $userRepo;

    /**
     * @var \DI\Container
     */
    static $container;

    public static function setUpBeforeClass(): void
    {
        self::$container = setup(true);

    }


    public function setUp(): void
    {
        $em = self::$container->get(\Doctrine\ORM\EntityManager::class);
        $schema_tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $classes = $em->getMetadataFactory()->getAllMetadata();
        $schema_tool->createSchema($classes);

        self::$container->injectOn($this);


        $this->assertInstanceOf(\API\Service\AdminService::class, $this->adminService);


    }

    public function testGet_current_admins()
    {

        $admin1 = new \API\Model\User("silweb", "silas.weber@uzh.ch", 1);
        $admin2 = new \API\Model\User("silweb2", "silas2.weber@uzh.ch", 1);
        $this->userRepo->save($admin1);
        $this->userRepo->save($admin2);

        $current_admins = $this->adminService->get_current_admins();
        $this->assertEquals(2, count($current_admins));

    }


    protected function tearDown(): void
    {
        $em = self::$container->get(\Doctrine\ORM\EntityManager::class);
        $schema_tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $classes = $em->getMetadataFactory()->getAllMetadata();
        $schema_tool->dropSchema($classes);
    }


}