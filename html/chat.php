<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 2/29/2016
 * Time: 8:41 AM
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/gossip/config/settings.php';

User::getCurrentUser();

$randomNode = GossipNode::getRandomNode();

$nodeURL = $randomNode->getNodeURL();

header("Location: http://$nodeURL/chat_interface.php");