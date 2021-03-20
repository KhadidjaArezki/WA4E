<?php 
	session_start();
	require_once "pdo.php";

	if ( ! isset($_SESSION['name']) ) {
    	die('Not logged in');
	}

	if (isset($_POST['logout'])) {
		header("Location: logout.php");
		return;
	}

	if ( isset($_POST['addnew']) ) {
		header("Location: add.php");
		return;
	}

	if ( isset($_POST['delete']) ) {
	    $sql = "DELETE FROM autosess WHERE auto_id = :zap";
	    $stmt = $pdo->prepare($sql);
	    $stmt->execute(array(':zap' => $_POST['auto_id']));
	    header("Location: view.php");
	    return;
	}

	$statement = $pdo->query("SELECT * FROM autosess ORDER BY make");
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
		</style>
		<div class="container">
			
			<?php
				
				echo ("<h2>Tracking Autos for ".$_SESSION['name']."</h2>"); 

				if ( isset($_SESSION['success']) ) {
	 				echo('<p style="color: green;">'.$_SESSION['success']."</p>\n");
	      			unset($_SESSION['success']);
				}

				if (isset($_SESSION['add_success'])) {
					echo('<p style="color: green;">'.$_SESSION['add_success']."</p>\n");
	      			unset($_SESSION['add_success']);
				}

			?>

			<h3>Automobiles</h3>
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

			<form method="post">
				<p><input type="submit" name="addnew" value="Add New">
				   <input type="submit" name="logout" value="Log out"></p>
			</form>
		</div>
	</body>

</html>
