<?php
	session_start();
	require_once "pdo.php";

	if ( ! isset($_SESSION['name']) ) {
	    	die('ACCES DENIED');
	}

	if (isset($_POST['cancel'])) {
		header('Location: index.php');
		return;
	}
	if ( isset($_POST['first_name']) && isset($_POST['last_name']) && 
			isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']) ) {

	 	if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 ||
	 		 strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
	 		$_SESSION['add_error'] = "A required field is missing";
	 		header("Location: add.php");
	 		return;
	 	}

	 	elseif (!strpos($_POST['email'], '@')) {
				$_SESSION['add_error'] = 'Email must have an at-sign (@)';
				header("Location: add.php");
				return;
		}
		else{
			$stmt = $pdo->prepare('INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary)
				VALUES ( :uid, :fn, :ln, :em, :he, :su)');

			$stmt->execute(array(
			  ':uid' => $_SESSION['user_id'],
			  ':fn' => $_POST['first_name'],
			  ':ln' => $_POST['last_name'],
			  ':em' => $_POST['email'],
			  ':he' => $_POST['headline'],
			  ':su' => $_POST['summary']));

			$_SESSION['success'] = "Profile Added";
		    header("Location: index.php");
		    return;
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
			font-family: sans-serif;
		}
	</style>
</head>
<body>
	<div class="container">
		<h1>Adding Profile for <?= $_SESSION['name']?></h1>
		<?php

			if ( isset($_SESSION['add_error']) ) {
	 				echo('<p style="color: red;">'.$_SESSION['add_error']."</p>\n");
	      			unset($_SESSION['add_error']);
			} 
		?>

		<form method="post"><br>
				<p>First Name:
					<input type="text" name="first_name" size="67"></p>
				<p>Last Name:
					<input type="text" name="last_name" size="67"></p>
				<p>Email:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="text" name="email" size="67"></p>
				<p>Headline:&nbsp;&nbsp;&nbsp;<br>
					<input type="text" name="headline" size="78"></p>
				<p>Summary:&nbsp;&nbsp;<br>
					<textarea name="summary" rows="10" cols="80" spellcheck="false"></textarea></p>
					
				<p><input type="submit" name="add" value="Add">
					<input type="submit" name="cancel" value="Cancel"></p>
			</form>
	</div>
</body>









