<?php
/**
 * Created by IntelliJ IDEA.
 * User: silas
 * Date: 16.12.19
 * Time: 17:31
 */


namespace API\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Firebase\JWT\JWT;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass="API\Repository\UserRepo")
 * @ORM\Table(name="user")
 */
class User
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;


    /**
     * @ORM\Column(type="string", unique=true)
     */
    private $email;


    /**
     * @ORM\Column(type="string")
     */
    private $username;

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username){
        $this->username = $username;
    }


    /**
     * @ORM\Column(type="integer")
     */
    private $userlevel;

    /**
     * @return int
     */
    public function getUserlevel()
    {
        return $this->userlevel;
    }


    /**
     * @ORM\OneToMany(targetEntity="Ticket", mappedBy="user", fetch="EAGER")
     */
    private $createdTickets;




    public function __construct(string $username, string $email, int $userlevel)
    {
        $this->username = $username;
        $this->email = $email;
        $this->userlevel = $userlevel;

        $this->createdTickets = new ArrayCollection();

    }

    public function addCreatedTicket(Ticket $ticket)
    {
        $this->createdTickets[] = $ticket;
    }

    /**
     * @return ArrayCollection
     */
    public function getCreatedTickets()
    {
        return $this->createdTickets;
    }


    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email){
        $this->email = $email;
    }

    /**
     * @param integer $userlevel
     */
    public function setUserlevel($userlevel): void
    {
        $this->userlevel = $userlevel;
    }



    public function getToken()
    {
        $token = array("id" => (int)$this->id, "userlevel" => (int)$this->userlevel);
        if (filter_var(getenv('PRODUCTION'), FILTER_VALIDATE_BOOLEAN)) {
            $jwt = JWT::encode($token, getenv('JWT_SECRET_KEY'));
        } else {
            sleep(5);
            $jwt = JWT::encode($token, getenv('JWT_SECRET_KEY'));
        }
        return $jwt;
    }



}