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

	if ( isset($_POST['make']) && isset($_POST['model']) && isset($_POST['mileage']) && isset($_POST['year'])) {

	 	if ( strlen($_POST['make']) < 1 || strlen($_POST['model']) < 1) {
	 		$_SESSION['add_error'] = "A required field is missing";
	 		header("Location: add.php");
	 		return;
	 	}
	 	elseif (!is_numeric($_POST['mileage']) || !is_numeric($_POST['year'])) {
	 		$_SESSION['add_error'] = "Mileage and year must be numeric.";
	 		header("Location: add.php");
	 		return;
	 	}
	 	elseif ( isset($_POST['url']) && (strlen($_POST['url']) > 1) 
	 				&& ( !str_starts_with($_POST['url'], "http://") && !str_starts_with($_POST['url'], "https://") ) ) { 
	 		
	 		$_SESSION['add_error'] = "Invalid URL";
	 		header("Location: add.php");
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
					header("Location: add.php");
					return;
				} 
	 		}

	 		$sql = "INSERT INTO autos (make, model, mileage, year, url) VALUES (:make, :model, :mileage, :year, :url)";
		 	$statement = $pdo->prepare($sql);
		 	$statement->execute(array(
		 		':make' => $_POST['make'],
		 		':model' => $_POST['model'],
		 		':mileage' => $_POST['mileage'],
		 		':year' => $_POST['year'],
		 		':url' => $_POST['url']));

		 	$_SESSION['success'] = "Record inserted";
		    header("Location: index.php");
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
				font-family: sans-serif;
			}
		</style>

		<div class="container">

			<?php 
				echo ("<h2>Tracking Autos for ".$_SESSION['name']."</h2>"); 

				if ( isset($_SESSION['add_error']) ) {
	 				echo('<p style="color: red;">'.$_SESSION['add_error']."</p>\n");
	      			unset($_SESSION['add_error']);
				}
			?>
			<form method="post">
				<p>Make:&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="text" name="make" size="40"></p>
				<p>Model:&nbsp;&nbsp;&nbsp;
					<input type="text" name="model" size="40"></p>
				<p>Mileage:
					<input type="number" name="mileage" size="40"></p>
				<p>Year:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="text" placeholder="YYYY"  name="year" size="40"></p>
				<p>Img_url:
					<input type="text" name="url" size="40"></p>
				<p><input type="submit" name="add" value="Add">
					<input type="submit" name="cancel" value="Cancel"></p>
			</form>
		</div>
	</body>

</html>