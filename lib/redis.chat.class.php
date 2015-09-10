<?php
/**
 * Created by PhpStorm.
 * User: rooty
 * Date: 10.09.15
 * Time: 11:41
 */
require_once 'redis.factory.php';
require_once 'RedisServiceChat.php';
class RedisChat extends RedisFactoryManager {

    public function createRoom($room_name)
    {
        if(empty($room_name)) return RedisServiceChat::Message('Enter the name of room');
        if(is_numeric($room_name)) return RedisServiceChat::Message('Name must be only string characters');
        try
        {
            if($this->handle->exists($room_name))
            {
                return RedisServiceChat::Message('Room is alredy exist');
            }
            else
            {
                if($this->handle->lPush($room_name, 'Moderator-|-'.date('Y-m-d h:m:s').'-|-Create room'))
                    return RedisServiceChat::Message('Room creating');
            }
        }
        catch(RedisException $e)
        {
            echo $e->getMessage();
            return RedisServiceChat::Message('Error on creating room');
        }
    }

    public function HandleRoom($room_name)
    {
        if(is_numeric($room_name)) return RedisServiceChat::Message('Name must be only string characters');
        try
        {
            if(!empty($messages = $this->handle->lRange($room_name, 0, -1)))
            {
                $mess = array();
                foreach($messages as $message)
                {
                    list($user, $date, $handler) = explode('-|-', $message);
                    $mess[] = array(
                        'user_name' => $user,
                        'date' => $date,
                        'message' => $handler
                    );
                }
                return $mess;
            }
            else
            {
                return RedisServiceChat::Message('Room does not exist or empty');
            }
        }
        catch (RedisException $e)
        {
            echo $e->getMessage();
        }
    }

    public function PushMessage($room_name, $user_name, $message)
    {
        if(empty($room_name) || empty($user_name) || empty($message)) return RedisServiceChat::Message('Please, try again');
        try
        {
            $this->handle->lPush($room_name, $user_name.'-|-'.date('H:m:s').'-|-'.$message);
        }
        catch (RedisException $e)
        {
            echo $e->getMessage();
        }
    }
}