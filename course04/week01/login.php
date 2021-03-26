<?php
	require_once "pdo.php";
	session_start();
	unset($_SESSION['name']);
	unset($_SESSION['user_id']);

	if (isset($_POST['cancel'])) {
	    // Redirect the browser to index.php
	    header("Location: index.php");
	    return;
	}

	$salt = 'XyZzy12*_';
	// $stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1';
	// $md5 = hash('md5', 'XyZzy12*_php123');

	if(isset($_POST['email']) && isset($_POST['password']) ) {
		unset($_SESSION['name']);

		if (strlen($_POST['email']) <= 0 || strlen($_POST['password']) <= 0) {

			$_SESSION['error'] = 'Email and password are required';
			header("Location: login.php");
			return;  
		}
		else {

			if (!strpos($_POST['email'], '@')) {
				$_SESSION['error'] = 'Email must have an at-sign (@)';
				header("Location: login.php");
				return;
			}
			else {
				$password = $salt.$_POST['password'];
				$check = hash('md5', $password);
				$statement = $pdo->prepare("SELECT user_id, name FROM users WHERE email = :email AND password = :password");
				$statement->execute(array(':email' => $_POST['email'], ':password' => $check));
				$row = $statement->fetch(PDO::FETCH_ASSOC);

				if ($row !== false) {
					error_log("Login Success ".$_POST['email']);
		    		$_SESSION['success'] = "Login Success";
					$_SESSION['name'] = $row['name'];
					$_SESSION['user_id'] = $row['user_id'];
					header("Location: index.php");
					return;
				}
				else {
					$_SESSION['error'] = 'Incorrect password';
		    		error_log("Login Failed ".$_POST['email']."$check");
		    		header("Location: login.php");
				    return;
				}	
		    }
		}
	}

?>

<!DOCTYPE html>
<html lang="en">
	<head>
    	<title>Khadidja Arezki</title>
    	<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" 
		    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
		    integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" 
		    crossorigin="anonymous">
		<style>
			body {
				font-family:sans-serif;
				margin: 40px;
			}
			
			label {
				font-weight: bold;
			}
		</style>
	</head>

	<body>
		
		<div class="container">

			<h1>Please Log In</h1>

			<?php 
			  if ( isset($_SESSION['error']) ) {
			  	echo ("<p style='color: red;'>".$_SESSION['error']."</p>\n"); 
			    unset($_SESSION['error']);
			  }  

			?>
			<!--acount is khadidjaarezki999@gmail.com
				password is 'php123' -->

			<form method="post" >
				<p><label for="email" >User Email</label>
					<input type="text" name="email" id="email" size="40">
				</p>
				<p><label for="password">Password&nbsp;&nbsp;</label>
					<input type="password" name="password" id="password" size="40">
				</p>
				<p><input type="submit" onclick="return validate();" name="login" value="Log In">
				   <input type="submit" name="cancel" value="Cancel">
				</p>
				<p>For a password hint, view source and find an account and password hint in the HTML comments.</p>

			</form>
			<script>
			function validate() {
			    //console.log('Validating...');
			    try {
			        email = document.getElementById('email').value;
			        password = document.getElementById('password').value; 
			        //console.log("Validating email="+ email+" password="+ password);
			        if (email == null || email == "" || password == null || password == "") {
			            alert("Both fields must be filled out");
			            return false;
			        }
			        if ( email.indexOf('@') == -1 ) {
			            alert("Invalid email address");
			            return false;
			        }
			        return true;
			    } catch(e) {
			        return false;
			    }
			    return false;
			}
			</script>
		</div>
	</body>
</html>