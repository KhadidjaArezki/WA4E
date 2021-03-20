<?php
	session_start();

	if (isset($_POST['cancel'])) {
	    // Redirect the browser to index.php
	    header("Location: index.php");
	    return;
	}

	$salt = 'XyZzy12*_';
	$stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1';
	$md5 = hash('md5', 'XyZzy12*_php123');

	if(isset($_POST['email']) && isset($_POST['password']) ) {
		unset($_SESSION['name']);

		if (strlen($_POST['email']) > 0 && strlen($_POST['password']) > 0) {
			
			if (!strpos($_POST['email'], '@')) {
				$_SESSION['error'] = 'Email must have an at-sign (@)';
				header("Location: login.php");
				return;
			}
			else {
				$password = $salt.$_POST['password'];
				$check = hash('md5', $password);
		    	if ($check !== $stored_hash) {
		    		$_SESSION['error'] = 'Incorrect password';
		    		error_log("Login Failed ".$_POST['email']."$check");
		    		header("Location: login.php");
				    return;
		    	}

		    	else {
		    		error_log("Login Success ".$_POST['email']);
		    		$_SESSION['success'] = "Logged in";
		    		$_SESSION['name'] = $_POST['email'];
		    		header("Location: view.php");
		    		return;
		    	}
		    }
		}

		else {
			$_SESSION['error'] = 'Email and password are required';
			header("Location: login.php");
			return;  
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
			  if ( isset($_SESSION['error']) ) {
			  	echo ("<p style='color: red;'>".$_SESSION['error']."</p>\n"); 
			    unset($_SESSION['error']);
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