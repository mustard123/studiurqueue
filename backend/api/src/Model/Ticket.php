<?php
/**
 * Created by IntelliJ IDEA.
 * User: silas
 * Date: 16.12.19
 * Time: 17:25
 */

namespace API\Model;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="API\Repository\TicketRepo")
 * @ORM\Table(name="ticket")
 */
class Ticket
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * Ticket constructor.
     * @param $creationDate
     * @param $status
     * @param $user
     */
    public function __construct($creationDate, $status, $user)
    {
        $this->creationDate = $creationDate;
        $this->status = $status;
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }


    /**
     * @ORM\Column(type="datetime")
     */
    private $creationDate;


    /**
     * @ORM\Column(type="string")
     */
    private $status;



    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="createdTickets", fetch="EAGER")
     */
    private $user;




    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }


    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user): void
    {
        $user->addCreatedTicket($this);
        $this->user = $user;
    }


    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
    }

    public function getCreationDate(): \DateTime
    {
        return $this->creationDate;
    }



}