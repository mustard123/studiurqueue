<?php
/**
 * Created by IntelliJ IDEA.
 * User: silas
 * Date: 4.03.19
 * Time: 17:26
 */

namespace API\Util;

use Firebase\JWT\JWT;

class JwtUtil
{

    static function jwt_is_valid ($jwt_string, $key){
        try {
            JWT::decode($jwt_string, $key, array('HS256'));
            return true;
        } catch (\Exception $e) {
            return false;

        }
    }

    static  function get_encoded_param  ($jwt_string, $key, $param){

        try {
            $decoded =  JWT::decode($jwt_string, $key, array('HS256'));

            return $decoded->{$param} ?? null;
        } catch (\Exception $e){
            echo $e;
            return null;
        }
    }

}