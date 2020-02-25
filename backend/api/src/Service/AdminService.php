<?php
/**
 * Created by IntelliJ IDEA.
 * User: silas
 * Date: 14.02.20
 * Time: 14:48
 */

namespace API\Service;


use API\Repository\UserRepo;

class AdminService
{

    public function __construct(UserRepo $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * @param $admins_to_add
     * @param $admins_to_remove
     * @return \API\Model\User[]
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function update_admins($admins_to_add, $admins_to_remove){

        $admins = $this->userRepo->findBy(array("email"=> $admins_to_add));
        foreach ($admins as $admin) {
            $admin->setUserlevel(1);
            $this->userRepo->persist($admin);
        }

        $admins = $this->userRepo->findBy(array("email"=> $admins_to_remove));
        foreach ($admins as $admin) {
            $admin->setUserlevel(0);
            $this->userRepo->persist($admin);
        }

        $this->userRepo->flush();
        return $this->get_current_admins();
    }

    /**
     * @return \API\Model\User[]
     */
    public function get_current_admins(){
        return $this->userRepo->findBy(array("userlevel" => 1));
    }

}