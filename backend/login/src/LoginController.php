<?php
/**
 * Created by IntelliJ IDEA.
 * User: silas
 * Date: 29.11.19
 * Time: 16:43
 */


namespace Login;

use API\Model\User;
use API\Repository\UserRepo;
use Doctrine\ORM\EntityManager;
use \Firebase\JWT\JWT;


/**
 * Class LoginController
 */
class  LoginController
{



    private $admin_mails = ["silas.weber@uzh.ch"];

    /**
     * LoginController constructor.
     */
    public function __construct(UserRepo $userRepo)
    {
        $this->userRepo = $userRepo;
    }


    public function sign_in($req, $res, $service)
    {
        $email = null;
        $given_name = null;

        if (filter_var(getenv('PRODUCTION'), FILTER_VALIDATE_BOOLEAN)) {

            // those are only set after successful SWITCH Login
            $email = $_SERVER['mail'];
            $given_name = $_SERVER['givenName'];

        } else {
            $email = "silas.weber@uzh.ch";
            $given_name = "silas";
        }

       try {
           $user = $this->get_or_create_user($email, $given_name);
           $service->message = "Du bist angemeldet, kehre zu Studiur Queue zurück. Du kannst dieses Fenster Schliessen";
           $service->jwtToken = $user->getToken();
           $service->render(__DIR__.'/login_view.html');
       } catch (\Exception $e) {
           $service->jwtToken = null;
           $service->message = $e->getMessage();
           $service->render(__DIR__.'/login_view.html');
           return;
       }

    }


    /**
     * @param string $email
     * @param string $username
     * @return User|null|object
     * @throws \Exception
     */
    private function get_or_create_user(string $email, string $username)
    {
        if (!isset($email) || !isset($username)) {
            throw new \Exception('User was not found and new one could not be created');

        } else {
            $user = $this->userRepo->findOneBy(array("email" => $email));

            if (!$user) {
                // if user does not exist we create one
                $new_user = $this->create_and_save_user($email, $username);
                return $new_user;

            } else {

                return $user;
            }
        }

    }


    /**
     * @param string $email
     * @param string $username
     * @return User
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function create_and_save_user(string $email, string $username)
    {
        $new_user = null;

        if (in_array($email, $this->admin_mails)) {
            $new_user = new User($username, $email, 1);
        } else {
            $new_user = new User($username, $email, 0);
        }

        $this->userRepo->save($new_user);

        return $new_user;
    }



}