<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 3/2/2016
 * Time: 6:13 PM
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/gossip/config/settings.php';

$users = User::getUsers();
$currentUser = User::getCurrentUser();

?>

<html>
    <head>
        <link href="css/style.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
    </head>
    <body>
    <div class="content-area">
        <div class="info-area">

            <?php if ($currentUser): ?>
                Logged in as: <?= $currentUser->getUsername(); ?>
            <?php endif; ?>

            <div class="account-buttons">
                <a href="login.php" class="btn btn-default" role="button">Login</a>
                <a href="create_account.php" class="btn btn-default" role="button">Create Account</a>
                <a href="logout.php" class="btn btn-default" role="button">Logout</a>
            </div>

        </div>

        <div>
            Users:
            <ul>
                <?php foreach ($users as $user): ?>
                    <li>
                        <a href="profile.php?userId=<?= $user->getUserId()?>"><?= $user->getUsername() ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <a href="chat.php" class="btn btn-default" role="button">Chat</a>
    </div>
    </body>
</html>