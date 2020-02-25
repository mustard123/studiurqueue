<?php
namespace SocketApp;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class StudQ implements MessageComponentInterface {
    protected $normalClients;
    protected $adminClients;

    private $connIdMap = array();



    public function __construct() {
        $this->normalClients = new \SplObjectStorage;
        $this->adminClients = new \SplObjectStorage();
    }

    public function onOpen(ConnectionInterface $conn) {

        $query_params_string = $conn->httpRequest->getURI()->getQuery();
        $params_array = [];
        parse_str($query_params_string, $params_array);
        $token = $params_array["token"];
        // Store the new connection to send messages to later
        if (\API\Util\JwtUtil::get_encoded_param($token, getenv("JWT_SECRET_KEY"), "userlevel") === 1){
            $this->connIdMap[$conn->resourceId] = 1;
            $this->adminClients->attach($conn);
        }
        else {
            $this->connIdMap[$conn->resourceId] = 0;
            $this->normalClients->attach($conn);
        }
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        return "OK";
    }


    public function onTicketEntry($notification) {
        $notification = json_decode($notification, true );
        echo json_encode($notification);
        foreach ($this->adminClients as $adminClient) {
            $adminClient->send(json_encode($notification));

        }
        foreach ($this->normalClients as $normalClient) {
            $this->remove_private_info($notification);
            $normalClient->send(json_encode($notification));

        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        if ($this->connIdMap[$conn->resourceId] == 0) {

            $this->adminClients->detach($conn);
        }
        else {
            $this->normalClients->detach($conn);
        }
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }


    private function remove_private_info(array &$ticket)
    {
        array_walk_recursive($ticket, function (&$item, $key) use ($ticket) {

            if ($key === 'email' || $key === "username") {
                $item = "";
            }

        });

    }
}