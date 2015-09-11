<?php
/**
 * Created by PhpStorm.
 * User: rooty
 * Date: 10.09.15
 * Time: 12:24
 */

error_reporting(1);
session_start();
require_once 'lib/redis.chat.class.php';
$red = new RedisChat(1);
if($_GET['view'])
{
    $room_name = empty($_SESSION['room']) ? $red->default_room : $_SESSION['room'];
    $messages = $red->HandleRoom($room_name);
    if($messages['service'] == 'Room does not exist or empty')
    {
        $red->createRoom('main');
        json_encode($red->HandleRoom('main'));
    }
    else
    {
        //var_dump($messages);
        echo json_encode($messages);
    }
}
if($_GET['write'])
{
    $room_name = empty($_SESSION['room']) ? $red->default_room : $_SESSION['room'];
    $red->PushMessage($room_name, $_SESSION['name'], $_GET['write']);
    echo json_encode(array('ok' => 1));
}
if($_GET['name'])
{
    $_SESSION['name'] = $_GET['name'];
    $_SESSION['room'] = empty($_GET['room']) ? $red->default_room : $_GET['room'];
}
if($_GET['session'])
{
    //var_dump($_SESSION);
    if(empty($_SESSION['name']))
    {
        echo json_encode(array('error' => 1));
    }
    else
    {
        echo json_encode(array('ok' => 1));
    }
}
if($_GET['rooms'])
{
    echo $red->GetRooms();
}
if($_GET['change_room'])
{
    $_SESSION['room'] = $_GET['change_room'];
}
if($_POST['room_name'])
{
    if($red->createRoom($_POST['room_name'])){
        echo json_encode(['ok' => 1]);
    }
}
if($_GET['change_room'])
{
    $_SESSION['room'] = $_GET['change_room'];
}
