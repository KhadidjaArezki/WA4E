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
	if (isset($_POST['search'])) {
		$statement= $pdo->prepare("SELECT profile_id, user_id, first_name, last_name, headline FROM Profile WHERE first_name LIKE :search");
		$statement->execute(array(':search' => $_POST['search']));
		$rows = $statement->fetchAll(PDO::FETCH_ASSOC);
	}
	else {
		if (!isset($_POST['next']) && !isset($_POST['back'])) {
			//$start = 0;
			$_SESSION['start'] = 0;
		}
		if (isset($_POST['next'])) {
			$_SESSION['start']+=10;
		}
		if (isset($_POST['back'])) {
			if ($_SESSION['start'] >= 10) {
				$_SESSION['start']-=10;
			}
		}
		$statement = $pdo->query("SELECT COUNT(*) FROM Profile");
		$_SESSION['count'] = $statement->fetch()[0];
		if ($_SESSION['start'] >= 10) {
			$_SESSION['count']-=10;
		}

		$sql = "SELECT profile_id, user_id, first_name, last_name, headline FROM Profile ORDER BY first_name LIMIT :start, 10";
		$statement = $pdo->prepare($sql);
		$statement->bindValue(':start', $_SESSION['start'], PDO::PARAM_INT);
		$statement->execute();
		$rows = $statement->fetchAll(PDO::FETCH_ASSOC);
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

		<!-- Load icon library -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<style>
		body {
			font-family: sans-serif;
		}
		td {
			text-align: center;
			padding: 2px;
		}
		form.search input[type=text] {
		  padding: 2px;
		  border: 1px solid grey;
		  float: left;
		  background: #f1f1f1;
		}
		/* Style the submit button */
		form.search button {
		  float: left;
		  width: 25px;
		  padding: 2px;
		  background: #2196F3;
		  color: white;
		  border: 1px solid grey;
		  border-left: none; /* Prevent double borders */
		  cursor: pointer;
		}

		form.search button:hover {
		  background: #0b7dda;
		}

		/* Clear floats */
		form.search::after {
		  content: "";
		  clear: both;
		  display: table;
		}
		/*.table {
			max-width: 410px;
		}*/
		#next {
			float: left;
		}
		#back {
			float: left;
		}
	</style>
</head>
<body>
	
	<div class="container">
	<?php

		if (!isset($_SESSION['name'])) {
			echo('<h1>Welcome To the Resume Registry</h1><br>');
			echo("<a href='login.php'>Please Log In</a>");
			// echo("<p>Attemp to <a href='add.php'>add data</a> without logging in should fail with an error message</p>");
		}
		else {
			echo ('<h1>Welcome To '.$_SESSION['name'].'\'s Resume Registry</h1><br>'); 

			if ( isset($_SESSION['success']) ) {
 				echo('<p style="color: green;">'.$_SESSION['success']."</p>\n");
      			unset($_SESSION['success']);
			}


			if (isset($_SESSION['error'])) {
				echo('<p style="color: red;">'.$_SESSION['error']."</p>\n");
      			unset($_SESSION['error']);
			}
		}

	?>
	<br><br>
	<form class="search" method="post">
	  <input type="text" placeholder="Search Registry..." name="search">
	  <button type="submit"><i class="fa fa-search"></i></button>
	</form>

	<div class="table">
	<?php
			// if($rows == false) {
			// 	echo "No rows found";
			// }
		if ($rows != false) {

			echo "<br><table border='2'>";
			echo "<tr><td>Name</td><td>Headline</td><td>Action</td></tr>";
			foreach ($rows as $row) {
				echo "<tr><td>";
				echo('<a href="view.php?profile_id='.$row['profile_id'].'">');
				echo(htmlentities($row['first_name']).' '.htmlentities($row['last_name']));
				echo "</a></td><td>";
				echo(htmlentities($row['headline']));
				echo "</td><td>";

				if ( isset($_SESSION['user_id']) && ($row['user_id'] === $_SESSION['user_id']) ) {
					echo("<a href= 'delete.php?profile_id=".$row['profile_id']."'> ");
				    echo('<input type="submit" value="Del" >');
				    echo("</a>");
				    echo("<a href= 'edit.php?profile_id=".$row['profile_id']."'> ");
				    echo('<input type="submit" value="edit">');
				    echo("</a>");
				}
				echo "</td></tr>\n";
			}
			echo "</table>";

			if (isset($_SESSION['count']) && $_SESSION['count'] > 10) {
				echo('<form method="post"><input id="next" type="submit" name="next" value= "Next">');
							
			}
			else {
				echo('<form method="post"><input id="next" type="submit" onclick="this.disabled=true;" name="next" value= "Next">');
			}

			if ($_SESSION['start'] == 0) {
				echo('<input id="back" type="submit" onclick="this.disabled=true;" name="back" value= "Back"></form>');
			}
			else {
				echo('<input id="back" type="submit" name="back" value= "Back"></form>');
			}
			echo("</div>");
			// echo($count);
		}
			
		if (isset($_SESSION['name'])) {
			echo "<br><form method='post'>";
			echo "<p><input type='submit' name='addnew' value='Add New Entry';>";
			echo "<input type='submit' name='logout' value='Log out'></p>";
			echo "</form>";
		}
	?>

	</div>
</body>
</html>