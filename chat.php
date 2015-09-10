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
    $messages = $red->HandleRoom('main');
    //var_dump($messages);
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
    $red->PushMessage('main', $_SESSION['name'], $_GET['write']);
    echo json_encode(array('ok' => 1));
}
if($_GET['name'])
{
    $_SESSION['name'] = $_GET['name'];
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
