
<?php
require_once '../config/settings.php';

// error_log('[mtapi][login.php]::$_REQUEST ' . print_r($_REQUEST, true));

$message = '';
if (isset($_REQUEST['username'])) {
	$username = $_REQUEST['username'];

	// error_log('[mtapi][login.php]::$username ' . print_r($username, true));

	if (User::isUser($username)) {
		$user = User::login($username);

		header('Location: profile.php?userId=' . $user->getUserId());
		die();
	}

	$message = "User $username does not exist.";

	// error_log('[mtapi][login.php]::$message ' . print_r($message, true));
	
}

?>
<html>
	
	<?= $message ?>
		
	<a href="index.php">Home</a>

	<form action="login.php" method="post">
	    
	    <div>
	        <label for="username">Username:</label>
	        <input type="text" name="username" id="username" />
	    </div>

	    <div class="button">
            <button type="submit">Login</button>
            <br>
			<a href="create_account.php">Create Account</a>
        </div>

	</form>

</html>