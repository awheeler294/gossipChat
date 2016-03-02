<?php
require_once '../config/settings.php';

// error_log('[mtapi][create_account.php]::$_REQUEST ' . print_r($_REQUEST, true));

$message = '';
if (isset($_REQUEST['username'])) {
	$username = $_REQUEST['username'];

	// error_log('[mtapi][create_account.php]::$username ' . print_r($username, true));

	if (!User::isUser($username)) {
		$user = User::createUser($username);

		header('Location: profile.php?userId=' . $user->getUserId());
		die();
	}

	$message = "User $username already exists.";

	// error_log('[mtapi][create_account.php]::$message ' . print_r($message, true));
	
}

?>

<html>
	
	<?= $message ?>
		
	<a href="index.php">Home</a>

	<form action="create_account.php" method="post">
	    
	    <div>
	        <label for="username">Username:</label>
	        <input type="text" name="username" id="username" />
	    </div>

	    <div class="button">
            <button type="submit">Signup</button>
            <br>
			<a href="login.php">Login</a>
        </div>

	</form>

</html>