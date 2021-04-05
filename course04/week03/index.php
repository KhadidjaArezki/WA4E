<?php
	session_start();
	require_once "pdo.php";
	require_once "util.php";

	if (isset($_POST['logout'])) {
		header("Location: logout.php");
		return;
	}

	if ( isset($_POST['addnew']) ) {
		header("Location: add.php");
		return;
	}
	if ( isset($_POST['clear_search']) ) {
		header("Location: index.php");
		return;
	}
	
	// Pulling data requested in the search field

	if (!isset($_POST['next']) && !isset($_POST['back'])) {
		//start is the row number we want pdo to start the select at
		$_SESSION['start'] = 0;
	}
	if (isset($_POST['next'])) {
		$_SESSION['start']+=10;
	}
	if (isset($_POST['back'])) {
		//if ($_SESSION['start'] >= 10) {
		$_SESSION['start']-=10;
		//}
	}
	// needs refactoring: create a page to display search results and redirect to it at post[search]
	// and move clear search button to it
	if (isset($_POST['search'])) {
		$_SESSION['count'] = 0;

		$statement= $pdo->prepare("SELECT profile_id, user_id, first_name, last_name, headline FROM Profile WHERE first_name LIKE :search");
		$statement->execute(array(':search' => $_POST['search']));
		$rows = $statement->fetchAll(PDO::FETCH_ASSOC);
	}
	else {
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
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Khadidja Arezki</title>
    <?php require_once "head.php"; ?>	
	<!-- Load icon library -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<!-- My styling -->
	<link rel="stylesheet" href="style.css">
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
			flashMessage();
		}

	?>
	<br>
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

			// next and back in case there are more than ten rows
			if (isset($_SESSION['count']) && $_SESSION['count'] > 10) {
				echo('<form method="post"><input id="next" type="submit" name="next" value= "Next">');				
			}
			else {
				echo('<form method="post"><input id="next" type="submit" name="next" value= "Next" disabled>');
			}

			if ($_SESSION['start'] == 0) {
				echo('<input id="back" type="submit" name="back" value= "Back" disabled></form>');
			}
			else {
				echo('<input id="back" type="submit" name="back" value= "Back"></form>');
			}

			echo("</div>");
			// echo($count);
		}
		if (isset($_POST['search'])) {
				echo '<br><form id="clear_search" method="post">';
				echo '<p><input type="submit" name="clear_search" value="Clear Search";>';
				echo '</form>';
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