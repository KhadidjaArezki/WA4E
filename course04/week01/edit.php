<?php
	session_start();
	require_once "pdo.php";

	if ( ! isset($_SESSION['name']) ) {
	    	die('ACCES DENIED');
	}

	if (isset($_POST['cancel'])) {
		header("Location: index.php");
		return;
	}

	if ( isset($_POST['first_name']) && isset($_POST['last_name']) && 
			isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']) ) {

	 	if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 ||
	 		 strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
	 		$_SESSION['add_error'] = "A required field is missing";
	 		header("Location: edit.php?profile_id=".$_POST['profile_id']);
	 		return;
	 	}

	 	elseif (!strpos($_POST['email'], '@')) {
				$_SESSION['add_error'] = 'Email must have an at-sign (@)';
				header("Location: edit.php?profile_id=".$_POST['profile_id']);
				return;
		}

		else{
			$stmt = $pdo->prepare('UPDATE Profile SET user_id=:uid, first_name=:fn, last_name=:ln, email=:em, headline=:he, summary=:su');

			$stmt->execute(array(
			  ':uid' => $_SESSION['user_id'],
			  ':fn' => $_POST['first_name'],
			  ':ln' => $_POST['last_name'],
			  ':em' => $_POST['email'],
			  ':he' => $_POST['headline'],
			  ':su' => $_POST['summary']));

			$_SESSION['success'] = "Profile Edited";
		    header("Location: index.php");
		    return;
		}
	}

	if (!isset($_GET['profile_id']) ) {
		$_SESSION['error'] = 'No Profile selected';
	    header("Location: index.php");
	    return;
	}

	$stmt = $pdo->prepare("SELECT * FROM Profile WHERE profile_id = :profile_id");
	$stmt->execute(array(':profile_id' => $_GET['profile_id']));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);

	if ($row == false) {
		$_SESSION['error'] = 'No Such Profile';
	    header("Location: index.php");
	    return;
	}

	if ($row['user_id'] != $_SESSION['user_id']) {
		$_SESSION['error'] = 'Permission Denied';
	    header("Location: index.php");
	    return;
	}

	$first_name = htmlentities($row['first_name']);
	$last_name = htmlentities($row['last_name']);
	$email = htmlentities($row['email']);
	$headline = htmlentities($row['headline']);
	$summary = htmlentities($row['summary']);
	$profile_id = htmlentities($row['profile_id']);
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
		<h1>Editing Profile for <?= $_SESSION['name']?></h1>
		<?php

			if ( isset($_SESSION['add_error']) ) {
	 				echo('<p style="color: red;">'.$_SESSION['add_error']."</p>\n");
	      			unset($_SESSION['add_error']);
			} 
		?>

		<form method="post"><br>
				<p>First Name:
					<input type="text" name="first_name" size="67" value="<?= $first_name ?>"></p>
				<p>Last Name:
					<input type="text" name="last_name" size="67" value="<?= $last_name ?>"></p>
				<p>Email:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="text" name="email" size="67" value="<?= $email ?>"></p>
				<p>Headline:&nbsp;&nbsp;&nbsp;<br>
					<input type="text" name="headline" size="78" value="<?= $headline ?>"></p>
				<p>Summary:&nbsp;&nbsp;<br>
					<textarea name="summary" rows="10" cols="80" spellcheck="false" ><?= $summary ?></textarea></p>
					
				<p><input type="submit" name="edit" value="Save">
					<input type="submit" name="cancel" value="Cancel"></p>
			</form>
	</div>
</body>