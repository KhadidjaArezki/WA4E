<?php 
	// session_start();
	if ( isset($_POST['cancel'] ) ) {
    // Redirect the browser to index.php
    header("Location: index.php");
    return;
	}

	//$redirect = "login.php";
	$message = false;
	$salt = 'XyZzy12*_';
	$stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1';
	$md5 = hash('md5', 'XyZzy12*_php123');

	if ($_POST) {
	  if ( isset($_POST['who']) && isset($_POST['pass']) && strlen($_POST['who']) > 0 && strlen($_POST['pass']) > 0) {

	  		$password = $salt.$_POST['pass'];
	    	if (hash('md5', $password) !== $stored_hash) {
	    		$message = 'Incorrect password';
	    	}
	    	
	    	else {
	    		// $_SESSION['who'] = $_POST['who'];
	    		header("Location: game.php?name=".urlencode($_POST['who']));
	    	}    	
	  }

	  else {
	  		$message = 'User name and password are required';  	
	  }
	}
?>

<!DOCTYPE html>
<html lang="en">
	<head>
    	<title>Khadidja Arezki</title>
	</head>

	<body>
		<style>
			body {
				font-family:sans-serif;
			
			label {
				font-weight: bold;
			}
		</style>
		<div class="container">

			<h1>Please Log In</h1>

			<?php 
			  if ($message !== false) {
			  	echo "<p style='color: red;'>$message</p>\n"; 
			  }  
			?>
			<!--password is 'php123' -->

			<form method="post" >
				<p><label for="who">User Name</label>
					<input type="text" name="who" id="who" size="40">
				</p>
				<p><label for="pass">Password&nbsp;&nbsp;</label>
					<input type="password" name="pass" id="pass" size="40">
				</p>
				<p><input type="submit" name="login" value="Log In">
				   <input type="submit" name="cancel" value="Cancel">
				</p>
				<p>For a password hint, view the source code and find a hint in the comments.</p>
			</form>
		</div>
	</body>
</html>