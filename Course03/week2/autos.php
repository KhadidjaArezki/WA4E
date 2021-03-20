<?php
	require_once "pdo.php";

	if(!isset($_GET['email'])) {
	 	die("Email parameter missing");
	}

	if (isset($_POST['logout'])) {
	 	header("Location: index.php");
	}

	 $message = false;
	 $color = "red";
	 $skip = false;

	if ( isset($_POST['delete']) ) {
	    $sql = "DELETE FROM autos WHERE auto_id = :zap";
	    $stmt = $pdo->prepare($sql);
	    $stmt->execute(array(':zap' => $_POST['auto_id']));
	}

	 if ( isset($_POST['make']) && isset($_POST['mileage']) && isset($_POST['year'])) {

	 	if (!is_numeric($_POST['mileage']) || !is_numeric($_POST['year'])) {
	 		$message = "Mileage and year must be numeric.";
	 	}
	 	elseif ( strlen($_POST['make']) < 1) {
	 		$message = "Make is required";
	 	}
	 	elseif ( isset($_POST['url']) && (strlen($_POST['url']) > 1) && 
	 				(!str_starts_with($_POST['url'], "http://")) && (!str_starts_with($_POST['url'], "https://")) ) { 
	 		
	 		$message = "Invalid URL";
	 	}

	 	else {

	 		if ( isset($_POST['url']) && (strlen($_POST['url']) > 1) ) {
	 		
		 		$curl_handle = curl_init();
				curl_setopt($curl_handle, CURLOPT_URL, $_POST['url']);
				// Receive server response ...
				curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);

				$server_output = curl_exec($curl_handle);

				curl_close ($curl_handle);

				// Further processing ...
				if (!$server_output) {
					//var_dump($server_output);
					$message = "This image URL does not exist.";
					$skip = true;
				} 
	 		}

	 		if (!$skip) {
	 		
		 		$sql = "INSERT INTO autos (make, mileage, year, url) VALUES (:make, :mileage, :year, :url)";
			 	$statement = $pdo->prepare($sql);
			 	$statement->execute(array(
			 		':make' => $_POST['make'],
			 		':mileage' => $_POST['mileage'],
			 		':year' => $_POST['year'],
			 		':url' => $_POST['url']));

			 	$message = "Record inserted";
			 	$color = "green";
			}
		 }
	 }

	 $statement = $pdo->query("SELECT * FROM autos ORDER BY make");
	 $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
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
			/*div {
				font-weight: bold;
			}*/
		</style>
		<div class="container">
			<h2>Welcome To Autos Database</h2>
			<p>Add a New Automobile</p>

			<?php echo ("<p style='color:$color'>$message</p>\n");?>

			<form method="post">
				<p>Make:&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="text" name="make" size="40"></p>
				<p>Mileage:
					<input type="number" name="mileage" size="40"></p>
				<p>Year:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="text" placeholder="YYYY"  name="year" size="40"></p>
				<p>Img_url:
					<input type="text" name="url" size="40"></p>
				<p><input type="submit" value="Add New">
					<input type="submit" name="logout" value="Log out"></p>
			</form>

			<table border="1">
				<tr><td>Make</td><td>Mileage</td><td>Year</td></tr>
				<?php
					foreach ($rows as $row) {
						echo "<tr><td>";
						echo ("<a href=".htmlentities($row['url']).">");
						echo (htmlentities($row['make']));
						echo "</a></td><td>";
						echo (htmlentities($row['mileage']));
						echo "</td><td>";
						echo (htmlentities($row['year']));
						echo "</td><td>";
						echo('<form method="post"><input type="hidden" ');
					    echo('name="auto_id" value="'.$row['auto_id'].'">'."\n");
					    echo('<input type="submit" value="Del" name="delete">');
					    echo("</form>");
						echo "</td></tr>\n";
					}
				?>
			</table>
		</div>
	</body>

</html>