<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 3/1/2016
 * Time: 11:17 PM
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/gossip/config/settings.php';

//error_log('[gossip][ajax.php]::$_REQUEST ' . print_r($_REQUEST, true));

$function = $_GET['function'];

echo call_user_func($function);

function saveMessage() {
    $order = $_POST['order'];
    $text = $_POST['text'];
    $currentUser = User::getCurrentUser();
    $originator = $currentUser->getUsername();

    if (!$originator) {
        $originator = 'Anonymous';
    }

    $messages = ChatMessage::saveLocalMessage($order, $originator, $text);

    $response = array('messages' => array());

    foreach($messages as $message) {
        $response['messages'][] = $message->toArray();
    }

    echo json_encode($response);
}

function pollForMessages() {



    $messages = ChatMessage::getMessages();

    $response = array('messages' => array());

    foreach($messages as $message) {
        $response['messages'][] = $message->toArray();
    }

    echo json_encode($response);
}