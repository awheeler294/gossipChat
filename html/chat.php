<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 2/29/2016
 * Time: 8:41 AM
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/gossip/config/settings.php';

User::getCurrentUser();

$nodes = ChatNode::getNodes();

$randomNode = rand(0, count($nodes) - 1);

$nodeURL = $nodes[$randomNode]->getNodeURL();

header("Location: http://$nodeURL/gossip/html/chat_interface.php");