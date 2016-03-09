<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 3/1/2016
 * Time: 11:17 PM
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/gossip/config/settings.php';

//error_log('[gossip][ajax.php]::$_REQUEST ' . print_r($_REQUEST, true));
//error_log('[Gossip][ajax.php] Headers: ' . print_r(getallheaders(), true));

$function = $_GET['function'];

echo call_user_func($function);

function saveMessage() {
    $order = $_POST['order'];
    $text = $_POST['text'];
    $currentUser = User::getCurrentUser();

    if (!$currentUser) {
        $originator = 'Anonymous';
    }
    else {
        $originator = $currentUser->getUsername();
    }

    $messages = ChatMessage::saveLocalMessage($order, $originator, $text);

    $response = array('messages' => array());

    foreach($messages as $message) {
        $response['messages'][] = $message->toArray();
    }

    echo json_encode($response);
}

function pollForMessages() {

    $messages = ChatMessage::pollForMessages();

    $response = array('messages' => array());

    foreach($messages as $message) {
        $response['messages'][] = $message->toArray();
    }

    echo json_encode($response);
}

function getOrder() {
    $order = ChatMessage::getOrder();

    $response = array('order' => $order);

    echo json_encode($response);
}

function addNode() {
    $nodeURL = $_POST['nodeURL'];

    GossipNode::saveNode($nodeURL);
}