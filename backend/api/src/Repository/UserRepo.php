<?php
/**
 * Created by IntelliJ IDEA.
 * User: silas
 * Date: 04.02.20
 * Time: 14:45
 */

namespace API\Repository;


use API\Model\User;
use Doctrine\ORM\EntityRepository;


/**
 * Class UserRepo
 * @package API\Repository
 */
class UserRepo extends EntityRepository
{
    /**
     *
     */
    public function clear()
    {
        parent::clear();
    }

    /**
     * @param mixed $id
     * @param null $lockMode
     * @param null $lockVersion
     * @return null|User
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
     * @return User[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param array $criteria
     * @param array|null $orderBy
     * @return null|User
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
     * @param User $user
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(User $user){
        $this->_em->persist($user);
        $this->_em->flush();

    }

    /**
     * @param User $user
     * @throws \Doctrine\ORM\ORMException
     */
    public function persist(User $user){
        $this->_em->persist($user);
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function flush(){
        $this->_em->flush();
    }






}