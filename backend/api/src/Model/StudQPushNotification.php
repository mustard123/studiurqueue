<?php
/**
 * Created by IntelliJ IDEA.
 * User: silas
 * Date: 11.12.19
 * Time: 13:32
 */

namespace API\Model;

use API\Model\Ticket;
use API\Model\User;

class StudQPushNotification
{

   public $header;
   public $payload;



    public function __construct(string $method, Ticket $payload)
    {
        $this->header = array();
        $this->header["action"] = $method; // 'CREATE', 'DELETE', 'START', 'CLOSE'
        $this->payload = $payload;

    }

}