<?php 
	session_start();
	require_once "pdo.php";
	require_once "util.php";

	if ( ! isset($_SESSION['name']) ) {
	    	die('ACCES DENIED');
	}

	if (isset($_POST['cancel'])) {
		header('Location: index.php');
		return;
	}

	if ( isset($_POST['first_name']) && isset($_POST['last_name']) && 
			isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']) ) {
		
		$message = validate_profile();
		if (is_string($message)) {
			$_SESSION['error'] = $message;
			header('Location: add.php');
			return;
		}

		//Validate position entries
		$message = validate_pos();
		if (is_string($message)) {
			$_SESSION['error'] = $message;
			header('Location: add.php');
			return;
		}

		//Validate education entries
		$message = validate_edu();
		if (is_string($message)) {
			$_SESSION['error'] = $message;
			header("Location: edit.php?profile_id=".$_POST['profile_id']);
			return;
		}

		// Data is valid, insert it
		$stmt = $pdo->prepare('INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary)
				VALUES ( :uid, :fn, :ln, :em, :he, :su)');

		$stmt->execute(array(
		  ':uid' => $_SESSION['user_id'],
		  ':fn' => $_POST['first_name'],
		  ':ln' => $_POST['last_name'],
		  ':em' => $_POST['email'],
		  ':he' => $_POST['headline'],
		  ':su' => $_POST['summary']));

	    $profile_id = $pdo->lastInsertId();

	    //insert the position an education entries
	    insert_pos($pdo, $profile_id);
	    insertEducations($pdo, $profile_id);

	    //Set up success message
	    $_SESSION['success'] = 'Profile added';
	    header('Location: index.php');
	    return;
	}

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
		<h1>Adding Profile for <?= $_SESSION['name'] ?></h1>
		<?php flashMessage(); ?>

		<form method="post"><br>
				<p>First Name:
					<input type="text" name="first_name" size="67"></p>
				<p>Last Name:
					<input type="text" name="last_name" size="67"></p>
				<p>Email:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="text" name="email" size="67"></p>
				<p>Headline:&nbsp;&nbsp;&nbsp;<br>
					<input type="text" name="headline" size="78"></p>
				<p>Summary:&nbsp;&nbsp;<br>
					<textarea name="summary" rows="10" cols="80" spellcheck="false"></textarea></p>
				<p>Education: <input type="submit" id="addedu" value="+">
					<div id="school_fields"></div></p>
				<p>Position: <input type="submit" id="addpos" value="+">
				 	<div id="position_fields"></div></p>
					
				<p><input type="submit" name="add" value="Add">
					<input type="submit" name="cancel" value="Cancel"></p>
		</form>
		<script>
			count_pos = 0;
			count_edu = 0;
			$(document).ready(function() {
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

				$('#addedu').click(function(event) {
					event.preventDefault();

					if (count_edu >= 9) {
						alert("Maximum entries number exceeded");
						return;
					}
					count_edu++;
					//Grab some html with hot spots and insert into the DOM
					var source = $('#edu_template').html();
					$('#school_fields').append(source.replace(/@COUNT@/g,count_edu));

					//Add the event handler to the new ones
					$('.school').autocomplete({source: 'school.php'});
				});
				$('.school').autocomplete({source: 'school.php'});
			});
		</script>

		<!-- HTML with substitution hot spots -->
		<script id="edu_template" type="text">
			<div id="edu@COUNT@">
				<p>Year: <input type="text" name="edu_year@COUNT@" value=""/>
					<input type="button" value="-" onclick="$('#edu@COUNT@').remove(); return false;"><br></p>
				<p>School: <input type="text" size="80" name="school@COUNT@" class="school" value=""/></p>
			</div>
		</script>
	</div>
</body>