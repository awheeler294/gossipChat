<?php
require_once '../config/settings.php';

$requestedUserId = $_REQUEST['userId'];

// error_log('[mtapt][profile.php]::$requestedUserId ' . print_r($requestedUserId, true));

$currentUser = User::getCurrentUser();

// error_log('[mtapt][profile.php]::$currentUser ' . print_r($currentUser, true));

$requestedUser = User::getUserById($requestedUserId);

// error_log('[mtapt][profile.php]::$requestedUser ' . print_r($requestedUser, true));

$ownProfile = false;

if ($currentUser) {
	$ownProfile = $currentUser->getUserId() == $requestedUser->getUserId();
	$checkins = $currentUser->getCheckins();
	// error_log('[mtapt][profile.php]::$ownProfile ' . print_r($ownProfile, true));
}


?>

<html>
	<div>
		<?php if ($currentUser): ?>
			Logged in as: <?= $currentUser->getUsername(); ?>
			<br>
	    <?php endif; ?>
		
		<a href="index.php">Home</a>
		<a href="login.php">Login</a>
		<a href="create_account.php">Create Account</a>
		<a href="logout.php">Logout</a>

	    <h1>
	    	<?= $requestedUser->getUsername(); ?>
	    </h1>

	    Last checkin: <?= $requestedUser->getLastCheckin(); ?>

		<?php if ($ownProfile): ?>
			<h5>Checkin History:</h5>
	        <ul>
	        	<?php foreach ($currentUser->getCheckins() as $checkin): ?>
			        <li>
			        	<?= $checkin['login_time']; ?>
			        </li>
			    <?php endforeach; ?>
	        </ul>
	    <?php endif; ?>

    </div>


</html>