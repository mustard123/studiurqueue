<?php
/**
 * Created by IntelliJ IDEA.
 * User: silas
 * Date: 14.02.20
 * Time: 14:47
 */

namespace API\Controller;


use API\Model\User;
use API\Service\AdminService;
use API\Util\SerializerHelper;

class AdminController
{

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }



    public function update_admins($req, $res, $service, $app){

        if ($app->userlevel != 1) {
            $res->code(400);
            return json_encode(array("message" => "You are not allowed to do this"));
        }

        $admin_settings = json_decode($req->body());

        if ($admin_settings == null){
            $res->code(400);
            return json_encode(array("message" => json_last_error_msg()));
        }

        try {

            $mails_to_add  = array();
            $mail_to_remove = array();

            $mails_to_add = $admin_settings->add;
            $mail_to_remove = $admin_settings->remove;

            $current_admins = $this->adminService->update_admins($mails_to_add, $mail_to_remove);
            $response = array_map(function (User $user){
                return $user->getEmail();
            }, $current_admins);
            return json_encode(array("currentAdmins"=>$response));


        } catch (\Exception $e){
            $res->code(400);
            return json_encode(array("message" => $e->getMessage()));
        }

    }



}