<?php
/**
 * Created by IntelliJ IDEA.
 * User: silas
 * Date: 07.02.20
 * Time: 16:15
 */

namespace API\Util;

use API\Model\StudQPushNotification;
use API\Model\Ticket;
use API\Model\User;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

abstract class SerializerHelper
{

    public static function getJson($object , $objectType = null, array $ignoredAttributes = [] ){

        $encoder = new JsonEncoder();
        $defaultContext = [
            \Symfony\Component\Serializer\Normalizer\AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
        ];
        $normalizer = new \Symfony\Component\Serializer\Normalizer\PropertyNormalizer(null, null, null, null, null, $defaultContext);

        $serializer = new Serializer([new \Symfony\Component\Serializer\Normalizer\DateTimeNormalizer(), $normalizer], [$encoder]);


        /*
         Ignore recurring fields. E.g User has Ticket which has field user, thus ignore property user in Ticket
         E.g Ticket has User which has propertiy createdTickets, thus ignore createdTickets
        This avaoids bloated json responses
        */
        if ($objectType){
            switch ($objectType){
                case User::class : {
                    $ignoredAttributes = array_merge($ignoredAttributes, ["user", "userlevel" ]);
                    break;
                }
                case Ticket::class : {
                    $ignoredAttributes = array_merge($ignoredAttributes, ["createdTickets", "userlevel"]);
                    break;
                }
                case StudQPushNotification::class : {
                    $ignoredAttributes = array_merge($ignoredAttributes, ["createdTickets", "userlevel"]);
                }
            }

        }

        return  $jsonContent = $serializer->serialize($object, 'json', [\Symfony\Component\Serializer\Normalizer\AbstractNormalizer::IGNORED_ATTRIBUTES => $ignoredAttributes]);

    }

}