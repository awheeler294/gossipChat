
<?php
require_once '../config/settings.php';

// error_log('[mtapi][login.php]::$_REQUEST ' . print_r($_REQUEST, true));

User::logout();

header('Location: chat.php');

die();