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
	// if user clicks on edit and auto_id  in form below is valid, update row in db
	if ( isset($_POST['make']) && isset($_POST['model']) && isset($_POST['mileage']) && isset($_POST['year'])) {

		if ( strlen($_POST['make']) < 1 || strlen($_POST['model']) < 1) {
	 		$_SESSION['add_error'] = "A required field is missing";
	 		header("Location: edit.php?auto_id=".$_POST['auto_id']);
	 		return;
	 	}
	 	elseif (!is_numeric($_POST['mileage']) || !is_numeric($_POST['year'])) {
	 		$_SESSION['add_error'] = "Mileage and year must be numeric.";
	 		header("Location: edit.php?auto_id=".$_POST['auto_id']);
	 		return;
	 	}
	 	elseif ( isset($_POST['url']) && (strlen($_POST['url']) > 1) 
	 				&& ( !str_starts_with($_POST['url'], "http://") && !str_starts_with($_POST['url'], "https://") ) ) { 
	 		
	 		$_SESSION['add_error'] = "Invalid URL";
	 		header("Location: edit.php?auto_id=".$_POST['auto_id']);
	 		return;
	 	}

	 	else {

	 		if ( isset($_POST['url']) && (strlen($_POST['url']) > 1) ){
	 		
		 		$curl_handle = curl_init();
				curl_setopt($curl_handle, CURLOPT_URL, $_POST['url']);
				// Receive server response ...
				curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);

				$server_output = curl_exec($curl_handle);

				curl_close ($curl_handle);

				// Further processing ...
				if (!$server_output) {
					//echo $server_output;
					$_SESSION['add_error'] = "This image URL does not exist.";
					header("Location: edit.php?auto_id=".$_POST['auto_id']);
					return;
				} 
	 		}
	 		$sql = "UPDATE autos SET make = :make, model = :model, mileage = :mileage, year = :year, url = :url WHERE auto_id = :auto_id";
		    $stmt = $pdo->prepare($sql);
		    $stmt->execute(array(
		        ':make' => $_POST['make'],
		        ':model' => $_POST['model'],
		        ':mileage' => $_POST['mileage'],
		        ':year' => $_POST['year'],
		        ':url' => $_POST['url'],
		        ':auto_id' => $_POST['auto_id']));
		    $_SESSION['success'] = 'Record updated';
		    header( 'Location: index.php' ) ;
		    return;
		}
	}

	if ( ! isset($_GET['auto_id']) ) {
	  $_SESSION['error'] = "No automobile selected";
	  header('Location: index.php');
	  return;
	}

	$statement = $pdo->prepare("SELECT * FROM autos WHERE auto_id = :xyz");
	$statement->execute(array(':xyz' => $_GET['auto_id']));	
	$row = $statement->fetch(PDO::FETCH_ASSOC);
	if ( $row === false ) {
	    $_SESSION['error'] = 'No such automobile Found';
	    header( 'Location: index.php' ) ;
	    return;
	}

	$make = htmlentities($row['make']);
	$model = htmlentities($row['model']);
	$mileage = htmlentities($row['mileage']);
	$year = htmlentities($row['year']);
	$url = htmlentities($row['url']);
	$auto_id = $row['auto_id'];
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
			<h2>Editing Automobile</h2>		
				<?php 
					if ( isset($_SESSION['add_error']) ) {
	 					echo('<p style="color: red;">'.$_SESSION['add_error']."</p>\n");
	      				unset($_SESSION['add_error']);
					}
					
				?>

			<form method="post">
					<p>Make:&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="text" name="make" size="40" value="<?= $make ?>"></p>
					<p>Model:&nbsp;&nbsp;&nbsp;
					<input type="text" name="model" size="40" value="<?= $model ?>"></p>
					<p>Mileage:
					<input type="number" name="mileage" size="40" value="<?= $mileage ?>"></p>
					<p>Year:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="text" placeholder="YYYY"  name="year" size="40" value="<?= $year ?>"></p>
					<p>Img_url:
					<input type="text" name="url" size="40" value="<?= $url ?>"></p>
					<input type="hidden"name="auto_id" value="<?= $auto_id ?>">
					<p><input type="submit" name="save" value="Save">
					<input type="submit" name="cancel" value="Cancel"></p>
				</form>
		</div>
	</body>

</html>

