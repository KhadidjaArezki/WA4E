<?php
	session_start();
	require_once "pdo.php";
	require_once "util.php";

	if ( ! isset($_SESSION['name']) ) {
	    	die('ACCES DENIED');
	}

	if (isset($_POST['cancel'])) {
		header("Location: index.php");
		return;
	}

	if ( isset($_POST['first_name']) && isset($_POST['last_name']) && 
			isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']) ) {

		$message = validate_profile();
		if (is_string($message)) {
			$_SESSION['error'] = $message;
			header("Location: edit.php?profile_id=".$_POST['profile_id']);
			return;
		}

		//Validate position entries
		$message = validate_pos();
		if (is_string($message)) {
			$_SESSION['error'] = $message;
			header("Location: edit.php?profile_id=".$_POST['profile_id']);
			return;
		}

		else{
			$stmt = $pdo->prepare('UPDATE Profile SET first_name=:fn, last_name=:ln, email=:em, headline=:he, summary=:su WHERE 
				profile_id = :pid AND user_id = :uid LIMIT 1');

			$stmt->execute(array(
			  ':pid' => $_REQUEST['profile_id'],
			  ':uid' => $_SESSION['user_id'],
			  ':fn' => $_POST['first_name'],
			  ':ln' => $_POST['last_name'],
			  ':em' => $_POST['email'],
			  ':he' => $_POST['headline'],
			  ':su' => $_POST['summary']));

			//Clear out the old position entries
			$stmt = $pdo->prepare("DELETE FROM position WHERE profile_id = :pid");
			$stmt->execute(array(':pid' => $_REQUEST['profile_id']));

			//Insert the position entries
			insert_pos($pdo, $_REQUEST['profile_id']);

			//Set update success message
			$_SESSION['success'] = "Profile Edited";
		    header("Location: index.php");
		    return;
		}
	}

	if (!isset($_GET['profile_id']) ) {
		$_SESSION['error'] = 'No Profile selected';
	    header("Location: index.php");
	    return;
	}

	//Load profile
	$stmt = $pdo->prepare("SELECT * FROM Profile WHERE profile_id = :profile_id");
	$stmt->execute(array(':profile_id' => $_GET['profile_id']));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);

	//Load positions
	$positions = load_pos($pdo, $_REQUEST['profile_id']);

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

	$first_name = htmlentities($row['first_name']);
	$last_name = htmlentities($row['last_name']);
	$email = htmlentities($row['email']);
	$headline = htmlentities($row['headline']);
	$summary = htmlentities($row['summary']);
	$profile_id = htmlentities($row['profile_id']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Khadidja Arezki</title>
    <?php require_once "head.php"; ?>
	<style>
		body {
			font-family: sans-serif;
		}
	</style>
</head>
<body>
	<div class="container">
		<h1>Editing Profile for <?= $_SESSION['name']?></h1>

		<?php flashMessage(); ?>

		<form method="post"><br>
				<input type="hidden" name="profile_id" value="<?= htmlentities($_GET['profile_id']); ?>">
				<p>First Name:
					<input type="text" name="first_name" size="67" value="<?= $first_name ?>"></p>
				<p>Last Name:
					<input type="text" name="last_name" size="67" value="<?= $last_name ?>"></p>
				<p>Email:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="text" name="email" size="67" value="<?= $email ?>"></p>
				<p>Headline:&nbsp;&nbsp;&nbsp;<br>
					<input type="text" name="headline" size="78" value="<?= $headline ?>"></p>
				<p>Summary:&nbsp;&nbsp;<br>
					<textarea name="summary" rows="10" cols="80" spellcheck="false" ><?= $summary ?></textarea></p>
				<p>Position: <input type="submit" id="addpos" value="+">
				 	<div id="position_fields">

				<?php
					if ($positions) {
					 	//$count_pos = 9 - count($positions);
					 	$i = 1;
					 	foreach ($positions as $position) {
					 		echo('<div id="position'.$i.'">'."\n");
					 		echo('<p>Year: <input type="text" name="year'.$i.'" value="'.htmlentities($position['year']).'"/>');
					 		echo('<input type="button" value="-" onclick="$(\'#position'.$i.'\').remove(); return false;"></p>');
					 		echo('<textarea name="desc'.$i.'" rows="8" cols="80" value="">'.htmlentities($position['description']).
					 			'</textarea></div>');
					 		$i++;
					 	}
					 } 
				 ?>

				 </div>
					
				<p><input type="submit" name="edit" value="Save">
					<input type="submit" name="cancel" value="Cancel"></p>
			</form>

			<script>

			$(document).ready(function() {
				count_pos = ($('#position_fields').find("div").length);
				$('#addpos').click(function(event) {
					event.preventDefault();

					if (count_pos >= 9) {
						alert("Maximum entries number exceeded");
						return;
					}

					count_pos++;
					$('#position_fields').append(
						'<div id="position'+count_pos+'">'+
						'<p>Year: <input type="text" name="year'+count_pos+'" value=""/>' +
						'<input type="button" value="-" onclick="$(\'#position' + count_pos +'\').remove(); return false;"></p>' +
						'<textarea name="desc'+count_pos+'" rows="8" cols="80"></textarea></div>'
					);
				});
			});
		</script>

	</div>
</body>