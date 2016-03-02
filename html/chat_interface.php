<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 2/29/2016
 * Time: 9:01 AM
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/gossip/config/settings.php';

$currentUser = User::getCurrentUser();

$node = ChatNode::build($_SERVER['HTTP_HOST']);

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
                Node ID: <?= $node->getNodeId(); ?>
                <br>
                <?php if ($currentUser): ?>
                    Logged in as: <?= $currentUser->getUsername(); ?>
                <?php endif; ?>

                <div class="account-buttons">
                    <a href="login.php" class="btn btn-default" role="button">Login</a>
                    <a href="create_account.php" class="btn btn-default" role="button">Create Account</a>
                    <a href="logout.php" class="btn btn-default" role="button">Logout</a>
                </div>

            </div>

            <div class="chat-area">

                <form>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="input-group">
                                <input type="text" class="form-control" id="chat-input" placeholder="Type Message Here">
                                <span class="input-group-btn">
                                    <input type="submit" class="btn btn-default" type="button" value="Send">
                                </span>
                            </div><!-- /input-group -->
                        </div><!-- /.col-lg-6 -->
                    </div><!-- /.row -->
                </form>

            </div>
        </div>
    </body>
</html>