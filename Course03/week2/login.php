<?php
	//print_r($_POST);
	if (isset($_POST['cancel'])) {
	    // Redirect the browser to index.php
	    header("Location: index.php");
	    return;
	}

	$message = false;
	$salt = 'XyZzy12*_';
	$stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1';
	$md5 = hash('md5', 'XyZzy12*_php123');

	if ($_POST) {
		if(isset($_POST['email']) && isset($_POST['password']) && strlen($_POST['email']) > 0 && strlen($_POST['password']) > 0) {
			
			if (!strpos($_POST['email'], '@')) {
				$message = 'Email must have an at-sign (@)';
			}
			else {
				$password = $salt.$_POST['password'];
				$check = hash('md5', $password);
		    	if ($check !== $stored_hash) {
		    		$message = 'Incorrect password';
		    		error_log("Login Failed ".$_POST['email']."$check");
		    	}

		    	else {
		    		error_log("Login Success ".$_POST['email']);
		    		header("Location: autos.php?email=".urlencode($_POST['email']));
		    	}
		    }
		}

		else {
			$message = 'Email and password are required';  
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
				<p><label for="email">User Email</label>
					<input type="text" name="email" id="email" size="40">
				</p>
				<p><label for="password">Password&nbsp;&nbsp;</label>
					<input type="password" name="password" id="password" size="40">
				</p>
				<p><input type="submit" name="login" value="Log In">
				   <input type="submit" name="cancel" value="Cancel">
				</p>
				<p>For a password hint, view the source code and find a hint in the comments.</p>

			</form>
		</div>
	</body>
</html>