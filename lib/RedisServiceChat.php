<?php
/**
 * Created by PhpStorm.
 * User: rooty
 * Date: 10.09.15
 * Time: 12:11
 */
class RedisServiceChat
{
    public static function Message($message)
    {
        return array('service' => $message);
    }
}