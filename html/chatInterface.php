<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 2/29/2016
 * Time: 9:01 AM
 */
require_once '../../config/settings.php';

$users = User::getUsers();
$currentUser = User::getCurrentUser();

$node = ChatNode::build($_SERVER['HTTP_HOST']);

?>

<html>
    <?php if ($currentUser): ?>
        Logged in as: <?= $currentUser->getUsername(); ?>
        <br>
    <?php endif; ?>

    Node ID: <?= $node->getNodeId(); ?>
</html>
