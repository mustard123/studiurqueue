<?php
/**
 * Created by IntelliJ IDEA.
 * User: silas
 * Date: 07.02.20
 * Time: 17:50
 */

namespace API\Service;


use API\Enum\HeaderAction;
use API\Enum\TicketStatus;
use API\Model\StudQPushNotification;
use API\Model\Ticket;
use API\Repository\TicketRepo;
use API\Repository\UserRepo;
use API\Util\SerializerHelper;

class TicketService
{

    public function __construct(UserRepo $userRepo, TicketRepo $ticketRepo)
    {
        $this->userRepo = $userRepo;
        $this->ticketRepo = $ticketRepo;
    }


    /**
     * @param $user_id
     * @return Ticket
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ZMQSocketException
     * @throws \Exception
     */
    public function create_ticket($user_id){

        $user = $this->userRepo->find($user_id);
        if (is_null($user)){
            throw new \Exception("User with id " . $user_id . " does not exists");
        }
        $ticket = new Ticket(new \DateTime(), TicketStatus::OPEN, $user);
        $user->addCreatedTicket($ticket);

        $this->ticketRepo->save($ticket);

        $context = new \ZMQContext(1, false);
        $socket = $context->getSocket(\ZMQ::SOCKET_PUSH);
        $socket->setSockOpt(\ZMQ::SOCKOPT_LINGER, 0 );

        $socket->connect("tcp://127.0.0.1:5555");

        $pushNotification = new StudQPushNotification(HeaderAction::CREATE, $ticket);
        $socket->send(SerializerHelper::getJson($pushNotification,Ticket::class), \ZMQ::MODE_DONTWAIT);


        return $ticket;

    }


    /**
     * @param bool $is_admin
     * @return Ticket[]
     */
    public function get_open_tickets($is_admin = false)
    {
        $tickets = $this->ticketRepo->findByStatus(TicketStatus::OPEN);

        if (!$is_admin) {
           foreach ($tickets as $ticket){
               // temporarily hide username and email for non admin clients
               $ticket->getUser()->setEmail("");
               $ticket->getUser()->setUsername("");
           }
        }

        return $tickets;
    }


    public function get_tickets_for_user($user_id){
        $tickets = $this->ticketRepo->findByUserId($user_id);
        return $tickets;
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null){
        return $this->ticketRepo->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param int $id
     * @return Ticket|null
     */
    public function findById(int $id){
        return $this->ticketRepo->find($id);
    }


    /**
     * @param $ticket_id
     * @return int
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
//    public function delete_ticket($ticket_id){
//        $id = $this->ticketRepo->deleteById($ticket_id);
//        return $id;
//    }


    public function close_ticket($ticket_id){
        $ticket  = $this->ticketRepo->find($ticket_id);
        $ticket->setStatus(TicketStatus::CLOSED);
        $this->ticketRepo->save($ticket);

        $context = new \ZMQContext(1, false);
        $socket = $context->getSocket(\ZMQ::SOCKET_PUSH);
        $socket->setSockOpt(\ZMQ::SOCKOPT_LINGER, 0 );
        $socket->connect("tcp://127.0.0.1:5555");

        $pushNotification = new StudQPushNotification('CLOSE', $ticket);
        $socket->send(json_encode($pushNotification));
        $socket->send(SerializerHelper::getJson($pushNotification, Ticket::class), \ZMQ::MODE_DONTWAIT);

        return $ticket->getId();
    }

    public function start_ticket($ticket_id)
    {

        $ticket  = $this->ticketRepo->find($ticket_id);
        $ticket->setStatus(TicketStatus::PROCESSING);
        $this->ticketRepo->save($ticket);

        $context = new \ZMQContext(1, false);
        $socket = $context->getSocket(\ZMQ::SOCKET_PUSH);
        $socket->setSockOpt(\ZMQ::SOCKOPT_LINGER, 0 );
        $socket->connect("tcp://127.0.0.1:5555");

        $pushNotification = new StudQPushNotification('START', $ticket);
        $socket->send(SerializerHelper::getJson($pushNotification, Ticket::class), \ZMQ::MODE_DONTWAIT);

        return $ticket;
    }
}