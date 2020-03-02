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
 * @ORM\Table(name="ticket_history")
 */
class TicketHistory
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * TicketHistory constructor.
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
     * @ORM\Column(type="datetime")
     */
    private $creationDate;


    /**
     * @ORM\Column(type="string")
     */
    private $status;



    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="createdTickets", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
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
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }



    public function getCreationDate(): \DateTime
    {
        return $this->creationDate;
    }



}