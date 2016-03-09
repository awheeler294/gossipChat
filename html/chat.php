<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 2/29/2016
 * Time: 8:41 AM
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/gossip/config/settings.php';

if (isset($_GET['userId'])) {
    $currentUser = User::setUser($_GET['userId']);
}
else {
    $currentUser = User::getCurrentUser();
}

if (!$currentUser) {
    $currentUser = new User(User::ANONYMOUS_USER_ID, User::ANONYMOUS_USER_NAME);
}

$randomNode = GossipNode::getRandomChatNode();

$nodeURL = $randomNode->getNodeURL();

$userId = $currentUser->getUserId();

header("Location: http://$nodeURL/chat_interface.php?userId=$userId");