<?php
	session_start();
	require_once "pdo.php";

	if (isset($_POST['logout'])) {
		header("Location: logout.php");
		return;
	}

	if ( isset($_POST['addnew']) ) {
		header("Location: add.php");
		return;
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
		a {
			text-decoration: : none;
		}
	</style>
	<h1>Welcome To Autos Database</h1>

	<?php

		if (!isset($_SESSION['name'])) {
			echo("<a href='login.php'>Please Log In</a>");
			echo("<p>Attemp to <a href='add.php'>add data</a> without logging in should fail with an error message</p>");
		}
		else {
			echo ("<h2>Tracking Autos for ".$_SESSION['name']."</h2>"); 

			if ( isset($_SESSION['success']) ) {
 				echo('<p style="color: green;">'.$_SESSION['success']."</p>\n");
      			unset($_SESSION['success']);
			}


			if (isset($_SESSION['error'])) {
				echo('<p style="color: red;">'.$_SESSION['error']."</p>\n");
      			unset($_SESSION['error']);
			}

			if($rows == false) {
				echo "No rows found";
			}
			else {
				echo "<table border='1'>";
				echo "<tr><td>Make</td><td>Model</td><td>Mileage</td><td>Year</td><td>Action</td></tr>";
				foreach ($rows as $row) {
					echo "<tr><td>";
					echo("<a href=".htmlentities($row['url']).">");
					echo(htmlentities($row['make']));
					echo "</a></td><td>";
					echo(htmlentities($row['model']));
					echo "</td><td>";
					echo(htmlentities($row['mileage']));
					echo "</td><td>";
					echo(htmlentities($row['year']));
					echo "</td><td>";
					echo("<a href= 'delete.php?auto_id=".$row['auto_id']."'> ");
				    echo('<input type="submit" value="Del" >');
				    echo("</a>");
				    echo("<a href= 'edit.php?auto_id=".$row['auto_id']."'> ");
				    echo('<input type="submit" value="edit">');
				    echo("</a>");
					echo "</td></tr>\n";
				}
				echo "</table>";
			}
		
			echo "<form method='post'>";
			echo "<p><input type='submit' name='addnew' value='Add New';>";
			echo "<input type='submit' name='logout' value='Log out'></p>";
			echo "</form>";
		}
	?>

	</body>
</html>
