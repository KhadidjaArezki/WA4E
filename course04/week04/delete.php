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

	if (isset($_POST['delete']) && isset($_POST['profile_id'])) {
		$sql = "DELETE FROM Profile WHERE profile_id = :zip";
	    $stmt = $pdo->prepare($sql);
	    $stmt->execute(array(':zip' => $_POST['profile_id']));
	    $_SESSION['success'] = 'Profile deleted';
	    header("Location: index.php");
	    return;
	}
	//ERROR HANDLING
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
		<h1>Deleting Profile</h1>
		<?php  

			echo('<p>First Name: '.htmlentities($row['first_name']).'</p>');
			echo('<p>Last Name: '.htmlentities($row['last_name']).'</p>');
		?>

		<form method="post">
				<input type="hidden"name="profile_id" value="<?= $row['profile_id'] ?>">
				<p><input type="submit" name="delete" value="delete">
				<input type="submit" name="cancel" value="Cancel"></p>
		</form>

	</div>
</body>
</html>