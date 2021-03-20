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
	// if user clicks on delete and auto_id  in form below is valid, DELETE ROW FROM DB
	if ( isset($_POST['delete']) && (isset($_POST['auto_id']))) {

	    $sql = "DELETE FROM autos WHERE auto_id = :zip";
	    $stmt = $pdo->prepare($sql);
	    $stmt->execute(array(':zip' => $_POST['auto_id']));
	    $_SESSION['success'] = 'Record deleted';
	    header("Location: index.php");
	    return;
	}
	//ERROR HANDLING
	if (!isset($_GET['auto_id']) ) {
		$_SESSION['error'] = 'No automobile selected';
	    header("Location: index.php");
	    return;
	}

	$statement = $pdo->prepare("SELECT auto_id, make, model FROM autos WHERE auto_id = :xyz");
	$statement->execute(array(':xyz' => $_GET['auto_id']));	
	$row = $statement->fetch(PDO::FETCH_ASSOC);
	if ( $row === false ) {
    $_SESSION['error'] = 'No such automobile Found';
    header( 'Location: index.php' ) ;
    return;
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
				font-family: sans-serif;
			}
		</style>

		<div class="container">

			<?php 
				echo ("<h2>Confirm: Deleting ".htmlentities($row['make'])." ".htmlentities($row['model'])."</h2>"); 
			?>

			<form method="post">
				<input type="hidden"name="auto_id" value="<?= $row['auto_id'] ?>">
				<p><input type="submit" name="delete" value="delete">
				<input type="submit" name="cancel" value="Cancel"></p>
			</form>
		</div>
	</body>

</html>
