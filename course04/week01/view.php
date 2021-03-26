<?php 
	session_start();
	require_once "pdo.php";

	if (!isset($_GET['profile_id'])) {
		$_SESSION['error'] = "No Profile Selected!";
		header("Location: index.php");
		return;
	}

	else {
		$stmt = $pdo->prepare("SELECT * FROM Profile WHERE profile_id = :profile_id");
		$stmt->execute(array(':profile_id' =>  $_GET['profile_id']));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($row == false) {
			$_SESSION['error'] = "No Such Profile!";
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
		<h1>Profile Information</h1>

		<?php  

			echo('<p>First Name: '.htmlentities($row['first_name']).'</p>');
			echo('<p>Last Name: '.htmlentities($row['last_name']).'</p>');
			echo('<p>Email: '.htmlentities($row['email']).'</p>');
			echo('<p>Headline: <br>'.htmlentities($row['headline']).'</p>');
			echo('<p>Summary: <br>'.htmlentities($row['summary']).'</p>');

		?>

		<a href="index.php">Done</a>
	</div>
</body>
</html>