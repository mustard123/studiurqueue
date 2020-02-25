<?php
/**
 * Created by IntelliJ IDEA.
 * User: silas
 * Date: 05.02.20
 * Time: 14:42
 */

namespace API\Repository;

use API\Enum\TicketStatus;
use API\Model\Ticket;
use API\Model\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query;

/**
 * Class TicketRepo
 * @package API\Repository
 */
class TicketRepo extends EntityRepository
{
    /**
     * @param mixed $id
     * @param null $lockMode
     * @param null $lockVersion
     * @return null|Ticket
     */
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        return parent::find($id, $lockMode, $lockVersion);
    }

    /**
     * @return array
     */
    public function findAll()
    {
        return parent::findAll();
    }

    /**
     * @param array $criteria
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     * @return Ticket[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param array $criteria
     * @param array|null $orderBy
     * @return null|Ticket
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return parent::findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @return int
     */
    public function count(array $criteria)
    {
        return parent::count($criteria);
    }


    /**
     * @param $status
     * @return Ticket[]
     */
    public function findByStatus($status){
        $q = $this->_em->createQueryBuilder()->select(array("t"))
            ->from(Ticket::class, "t")
            ->join("t.user", "u")
            ->where("t.status = ?1 ")
            ->addSelect("u")
            ->orderBy("t.creationDate", "ASC")
            ->setParameter(1, $status)
            ->getQuery()->setFetchMode(Ticket::class,"user",ClassMetadata::FETCH_EAGER);
        $openTickets = $q->execute();
        return $openTickets;
    }


    /**
     * @param $status
     * @return Ticket[]
     */
    public function findByUserId($user_id){
        $qb = $this->_em->createQueryBuilder();
        $q=$qb->select(array("t"))
                ->from(Ticket::class, "t")
                ->join("t.user", "u")
                ->where("u.id = ?1 ")
                ->andWhere($qb->expr()->not("t.status = 'closed'"))
                ->orderBy("t.creationDate", "DESC")
                ->setParameter(1, $user_id)
                ->getQuery();
        $tickets = $q->execute();
        return $tickets;
    }


    /**
     * @param int $ticket_id
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @return int
     */
    public function deleteById($ticket_id){
        $ticket = $this->_em->getReference(Ticket::class, $ticket_id);
        $this->_em->remove($ticket);
        $this->_em->flush();
        return $ticket_id;

    }


    /**
     * @param Ticket $user
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Ticket $ticket){
        $this->_em->persist($ticket);
        $this->_em->flush();

    }


}