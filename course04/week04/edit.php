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
	 // Handle post data
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

		//Validate education entries
		$message = validate_edu();
		if (is_string($message)) {
			$_SESSION['error'] = $message;
			header("Location: edit.php?profile_id=".$_POST['profile_id']);
			return;
		}

		//Begin updating the data
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

		//Clear out old education entries
		$stmt = $pdo->prepare('DELETE FROM Education WHERE profile_id = :pid');
		$stmt->execute(array(':pid' => $_REQUEST['profile_id'])); 

		//Insert education entries
		insertEducations($pdo, $_REQUEST['profile_id']);

		//Set update success message
		$_SESSION['success'] = "Profile Edited";
	    header("Location: index.php");
	    return;
	}
	 // In case profile_id param was not passed with get request
	if (!isset($_GET['profile_id']) ) {
		$_SESSION['error'] = 'No Profile selected';
	    header("Location: index.php");
	    return;
	}

	//Load profile
	$stmt = $pdo->prepare("SELECT * FROM Profile WHERE profile_id = :profile_id");
	$stmt->execute(array(':profile_id' => $_GET['profile_id']));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);

	//Load positions and education
	$positions = load_pos($pdo, $_REQUEST['profile_id']);
	$educations = load_edu($pdo, $_REQUEST['profile_id']);

	//In case profile not found
	if ($row == false) {
		$_SESSION['error'] = 'No Such Profile';
	    header("Location: index.php");
	    return;
	}

	//In case profile is not owned by editor
	if ($row['user_id'] != $_SESSION['user_id']) {
		$_SESSION['error'] = 'Permission Denied';
	    header("Location: index.php");
	    return;
	}

	// Store data for fall through
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
				<p>Education: <input type="submit" id="addedu" value="+">
					<div id="school_fields">
					<?php 

					if ($educations) {
						$i = 1;
						foreach($educations as $edu) {
							echo('<div id="edu'.$i.'">'."\n");
							echo('<p>Year: <input type="text" name="edu_year'.$i.'" value="'.htmlentities($edu['year']).'"/>');
							echo('<input type="button" value="-" onclick="$(\'#edu'.$i.'\').remove(); return false;"></p>');
							echo('<p>School: <input type="text" size="80" name="school'.$i.'" class="school" value="'.htmlentities($edu['name']).'"/></p>');
							$i++;
						}
					}
					?>
					</div></p>
				<p>Position: <input type="submit" id="addpos" value="+">
				 	<div id="position_fields">

				<?php
					if ($positions) {
					 	//$count_pos = 9 - count($positions);
					 	$j = 1;
					 	foreach ($positions as $position) {
					 		echo('<div id="position'.$j.'">'."\n");
					 		echo('<p>Year: <input type="text" name="year'.$j.'" value="'.htmlentities($position['year']).'"/>');
					 		echo('<input type="button" value="-" onclick="$(\'#position'.$j.'\').remove(); return false;"></p>');
					 		echo('<textarea name="desc'.$j.'" rows="8" cols="80" value="">'.htmlentities($position['description']).
					 			'</textarea></div>');
					 		$j++;
					 	}
					 } 
				 ?>
				 	</div></p>
					
				<p><input type="submit" name="edit" value="Save">
					<input type="submit" name="cancel" value="Cancel"></p>
			</form>

			<script>

			$(document).ready(function() {
				// count_pos = <?= $j ?>;
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

				count_edu = ($('#school_fields').find('div').length);
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
				// window.console && console.log('Requesting JSON');
				// $.getJSON('school.php', function(rowz){
				// 	window.console && console.log('JSON Received');
				// 	window.console && console.log(rowz);
				// });
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
</html>