<?php
/**
 * Created by IntelliJ IDEA.
 * User: silas
 * Date: 10.02.20
 * Time: 13:27
 */

require_once __DIR__."/../../bootstrap.php";
use PHPUnit\Framework\TestCase;





class TicketServiceTest extends TestCase
{
    /**
     * @\DI\Annotation\Inject()
     * @var \API\Service\TicketService
     */
    private $ticketService;

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


        $this->assertInstanceOf(\API\Service\TicketService::class, $this->ticketService);


    }

    public function testCreateTicket(){
        $user = new \API\Model\User("testuser", "testuser@uzh.ch", 0);
        $this->userRepo->save($user);

        $ticket = $this->ticketService->create_ticket($user->getId());
        self::assertEquals(1, $ticket->getId());
    }

    public function testGetOpenTickets(){
        $user = new \API\Model\User("testuser", "testuser@uzh.ch", 0);
        $this->userRepo->save($user);

        $this->ticketService->create_ticket($user->getId());
        $this->ticketService->create_ticket($user->getId());
        $this->ticketService->create_ticket($user->getId());
        $this->ticketService->create_ticket($user->getId());

        $tickets = $this->ticketService->get_open_tickets();
        $this->assertEquals(4, count($tickets));

        foreach ($tickets as $ticket){
            $this->assertEmpty($ticket->getUser()->getEmail());
            $this->assertEmpty($ticket->getUser()->getUsername());
        }

    }

    public function testStartTicket(){
        $user = new \API\Model\User("testuser", "testuser@uzh.ch", 0);
        $this->userRepo->save($user);

        $ticket = $this->ticketService->create_ticket($user->getId());

        $ticket = $this->ticketService->start_ticket($ticket->getId());

        $this->assertEquals(\API\Enum\TicketStatus::PROCESSING, $ticket->getStatus());
        $this->assertEquals(count($this->ticketService->ticketRepo->findByStatus(\API\Enum\TicketStatus::PROCESSING)),1);

    }

    protected function tearDown(): void
    {
        $em = self::$container->get(\Doctrine\ORM\EntityManager::class);
        $schema_tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $classes = $em->getMetadataFactory()->getAllMetadata();
        $schema_tool->dropSchema($classes);
    }


}
