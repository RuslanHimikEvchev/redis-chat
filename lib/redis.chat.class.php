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

    //Expire life time room
    private $room_expire = 86400;

    //set on public rooms list
    private $rooms_hash = 'public_rooms';

    //default room name
    public $default_room = 'main';
    public $users = 'users';

    //serialization message data
    private static function adapt($array = array(), $decode = false)
    {
        if(is_array($array) || is_string($array))
            if(!$decode)
                return serialize($array);
            else
                return unserialize($array);
        return false;
    }

    /**
     * @param $room_name (string)
     * @return array (service message strings)
     */
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
                if($this->handle->lPush($room_name, $this->adapt(array(
                            'user_name' => 'Moderator',
                            'date' => date('H:i:s'),
                            'message' => 'create room '.$room_name
                ))))
                    if(!$this->handle->sIsMember($this->rooms_hash, $room_name))
                        if($this->handle->sAdd($this->rooms_hash, $room_name))
                    {
                        $this->handle->expire($room_name, $this->room_expire);
                        return RedisServiceChat::Message('Room creating');
                    }
            }
        }
        catch(RedisException $e)
        {
            echo $e->getMessage();
            return RedisServiceChat::Message('Error on creating room');
        }
    }

    /**
     * @param $room_name
     * @return array (of messages)
     */

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
                    $mess_arr = $this->adapt($message, true);
                    $mess[] = $mess_arr;
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

    /**
     * @param $room_name (string)
     * @param $user_name (string or int)
     * @param $message (string or int)
     * @return array (service message array)
     */
    public function PushMessage($room_name, $user_name, $message)
    {
        if(empty($room_name) || empty($user_name) || empty($message)) return RedisServiceChat::Message('Please, try again');
        try
        {
            $this->handle->lPush($room_name, $this->adapt(array(
                'user_name' => $user_name,
                'date' => date('H:i:s'),
                'message' => $message
            )));
        }
        catch (RedisException $e)
        {
            echo $e->getMessage();
        }
    }

    /**
     * @return bool|string (list of public rooms)
     */
    public function GetRooms()
    {
        if($rooms = $this->handle->sMembers($this->rooms_hash))
        {
            //$rooms = $this->json($rooms, true);
            //var_dump($rooms);
            foreach($rooms as $key => $room)
            {
                if(!$this->handle->exists($room))
                {
                    $this->handle->sRem($this->rooms_hash, $room);
                    unset($rooms[$key]);
                }
            }
            return json_encode($rooms);
        }
        return false;
    }

    public function CreateUser($user_name)
    {
        if(empty($user_name) || strlen($user_name) > 14) return RedisServiceChat::Message('Name must be 14 characters or lower');
        try
        {
            $this->handle->sAdd($this->users, $user_name);
        }
        catch(RedisException $e)
        {
            echo $e->getMessage();
            return false;
        }
        return RedisServiceChat::Message('Creating user: '.$user_name);
    }

    public function GetUsers()
    {
        try
        {
            $users = $this->handle->sMembers($this->users);
        }
        catch (RedisException $e)
        {
            echo $e->getMessage();
            return false;
        }
        if(!empty($users))
        {
            return json_encode($users);
        }
        else
        {
            return false;
        }
    }
}