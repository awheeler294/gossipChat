<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 3/1/2016
 * Time: 11:17 PM
 */

$function = $_GET['function'];

echo call_user_func($function);

function saveMessage() {
    $order = $_POST['order'];
    $text = $_POST['text'];
    $originator = User::getCurrentUser();

    if (!$originator) {
        $originator = 'Anonymous';
    }

    $messages = ChatMessage::saveMessage($order, $originator, $text);

    $messagesJSON = array();

    foreach($messages as $message) {
        $messagesJSON[] = $message->toArray();
    }

    echo json_encode($messagesJSON);
}