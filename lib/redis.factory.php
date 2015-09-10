<?php

/**
 * Created by PhpStorm.
 * User: Администратор
 * Date: 01.09.2015
 * Time: 0:40
 */
class RedisFactoryManager
{

    public static $expire = 600;
    public $redis_conf = array(
        1 => array(
            'host' => 'localhost',
            'port' => 6379
        )
    );
    public $max_lenght = 15;

    public function __construct($conf_key)
    {
        try {
            $this->handle = new Redis();
            $this->handle->connect(
                $this->redis_conf[$conf_key]['host'],
                $this->redis_conf[$conf_key]['port']
            );
        } catch (RedisException $e) {
            die($e->getMessage());
        }
    }

    public function ping()
    {
        return $this->handle->ping();
    }

    public function getKeys()
    {
        return $this->handle->keys("**");
    }

    public function hSetServiceData($status)
    {
        if (empty($status)) return false;
        try {
            $this->handle->hMset('service', array(
                'date' => date('Y-m-d h:m:s'),
                'status' => $status,
                'sessions' => count($this->handle->hMGet('service', array('sessions'))) + 1
            ));
        } catch (RedisException $e) {
            echo $e->getMessage();
        }
        return true;
    }
}