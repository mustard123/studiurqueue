<?php
/**
 * Created by IntelliJ IDEA.
 * User: silas
 * Date: 04.12.19
 * Time: 13:07
 */
namespace API\Controller;
use API\Model\Ticket;
use API\Service\TicketService;
use API\Util\SerializerHelper;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;


/**
 * Class TicketController
 * @package API
 */
class TicketController
{

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }


    public function post_ticket($req, $res, $service, $app)
    {
        try {
            $ticket = $this->ticketService->create_ticket($app->user_id);
            $res->code(201);
            return json_encode(array("ticketId" => $ticket->getId()));
        } catch (\Exception $e){
            error_log($e);
            $res->code(400);
            return json_encode(array("message" => $e->getMessage()));
        }
    }

    public function get_open_tickets($req, $res, $service, $app)
    {
            $is_admin = $app->userlevel == 1 ? true : false;
            $openTickets = $this->ticketService->get_open_tickets($is_admin);
            return SerializerHelper::getJson($openTickets, Ticket::class);
    }

    public function get_user_tickets($req, $res, $service, $app)
    {
        $is_admin = $app->userlevel == 1 ? true : false;
        $req_user_id = $req->user_id;
        $user_id = $app->user_id;

        if (($user_id !== $req_user_id) || !$is_admin){
            $res->code(405);
            return json_encode(array("message"=> "You are not allowed to access this resource"));
        }

        $userTickets = $this->ticketService->get_tickets_for_user($req_user_id);

        return SerializerHelper::getJson($userTickets, Ticket::class);
    }

    public function delete_ticket($req, $res, $service, $app)
    {

        $is_admin = $app->userlevel == 1 ? true : false;
        $req_ticket_id = $req->ticket_id;
        $user_id = $app->user_id;

        $ticket = $this->ticketService->findById($req_ticket_id);

        if (is_null($ticket)){
            $res->code(400);
            return json_encode(array("message"=> "Resource not found"));
        }

        if (($user_id !== $ticket->getUser()->getId()) && !$is_admin){
            $res->code(405);
            return json_encode(array("message"=> "You are not allowed to access this resource"));
        }

        try {
            $ticketId = $this->ticketService->delete_ticket($req_ticket_id);
        } catch (\Exception $e) {
            $res->code(500);
            return json_encode(array("message"=> $e->getMessage()));
        }

        return $ticketId;
    }


    public function close_ticket($req, $res, $service, $app)
    {

        $is_admin = $app->userlevel == 1 ? true : false;
        $req_ticket_id = $req->ticket_id;
        $user_id = $app->user_id;

        $ticket = $this->ticketService->findById($req_ticket_id);

        if (is_null($ticket)){
            $res->code(400);
            return json_encode(array("message"=> "Resource not found"));
        }

        if (($user_id !== $ticket->getUser()->getId()) && !$is_admin){
            $res->code(405);
            return json_encode(array("message"=> "You are not allowed to access this resource"));
        }

        try {
            $ticketId = $this->ticketService->close_ticket($req_ticket_id);

        } catch (\Exception $e) {
            $res->code(500);
            return json_encode(array("message"=> $e->getMessage()));
        }

        return $ticketId;
    }

    public function start_ticket($req, $res, $service, $app)
    {

        $is_admin = $app->userlevel == 1 ? true : false;
        $req_ticket_id = $req->ticket_id;
        $user_id = $app->user_id;

        $ticket = $this->ticketService->findById($req_ticket_id);

        if (is_null($ticket)){
            $res->code(400);
            return json_encode(array("message"=> "Resource not found"));
        }

        if (($user_id !== $ticket->getUser()->getId()) && !$is_admin){
            $res->code(405);
            return json_encode(array("message"=> "You are not allowed to access this resource"));
        }

        try {
            $ticket = $this->ticketService->start_ticket($req_ticket_id);

        } catch (\Exception $e) {
            $res->code(500);
            error_log($e);
            return json_encode(array("message"=> $e->getMessage()));
        }

        return json_encode($ticket->getId());
    }




}