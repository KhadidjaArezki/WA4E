<?php 
	session_start();
	require_once "pdo.php";
	require_once "util.php";

	if (!isset($_GET['profile_id'])) {
		$_SESSION['error'] = "No Profile Selected!";
		header("Location: index.php");
		return;
	}

	else {
		$stmt = $pdo->prepare("SELECT * FROM Profile WHERE profile_id = :profile_id");
		$stmt->execute(array(':profile_id' =>  $_GET['profile_id']));
		$profile = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($profile == false) {
			$_SESSION['error'] = "No Such Profile!";
			header("Location: index.php");
			return;
		}

		$positions = load_pos($pdo, $_GET['profile_id']);
		$educations = load_edu($pdo, $_GET['profile_id']);
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

			echo('<p>First Name: '.htmlentities($profile['first_name']).'</p>');
			echo('<p>Last Name: '.htmlentities($profile['last_name']).'</p>');
			echo('<p>Email: '.htmlentities($profile['email']).'</p>');
			echo('<p>Headline: <br>'.htmlentities($profile['headline']).'</p>');
			echo('<p>Summary: <br>'.htmlentities($profile['summary']).'</p>');

			if ($positions) {
				echo('Positions: <ul>');
				foreach ($positions as $position) { 
					echo('<li>'.htmlentities($position['year']).': '.htmlentities($position['description']).'</li>');
				}
				echo('</ul>');
			}

			if ($educations) {
				echo('Educations: <ul>');
				foreach ($educations as $education) { 
					echo('<li>'.htmlentities($education['year']).': '.htmlentities($education['name']).'</li>');
				}
				echo('</ul>');
			}

		?>

		<a href="index.php">Done</a>
	</div>
</body>
</html>