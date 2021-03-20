<?php
	//session_start();
	// This function takes as its input the computer and human play
	// and returns "Tie", "You Lose", "You Win" depending on play
	// where "You" is the human being addressed by the computer
	if (isset($_GET['name'])){
	// if (isset($_SESSION['who']) && strlen($_SESSION['who']) > 0) {
		$username = $_GET['name'];
	}
	else {
		die("Name parameter missing");
	}

	if ( isset($_POST['logout']) ) {
    header('Location: index.php');
    return;
	}
	//$human = isset($_POST["human"]) ? $_POST['human']+0 : -1;

	function check($computer, $human) {
		if ($computer === $human ) {
			return "Tie";
		}
		elseif (($computer === 'Rock' && $human === 'Scissors') || ($computer === 'Scissors' && $human === 'Paper') || ($computer === 'Paper' && $human === 'Rock') ) {
			return "You Lose";
		}
		else return "You Win";
	}
	
	$names = array('Rock', 'Paper', 'Scissors');
	$game = '';
	if (isset($_POST['play'] )) {
		$computer = $names[array_rand($names)];
		if ($_POST['pick'] !== 'test') {
			$game = "Human = ".$_POST['pick']." Computer = ".$computer." Result =  ".check($computer, $_POST['pick'])."\n";
		}
		else {

			for($c = 0; $c < 3; $c++) {
			    for($h = 0; $h < 3; $h++) {
			        $r = check($names[$c], $names[$h]);
			        $game.= nl2br("Computer = $names[$c] Human = $names[$h] Result=$r\n\n");
			    }
			}
		}
	}
	else $game = 'Please select a strategy and click Play.';




	//$logout = header('Location: index.php, $response_code = 200');

	// session_unset();
	// session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<?php //require_once "bootstrap.php"; ?>
    	<title>Khadidja Arezki</title>
	</head>
	<body>
		<style>
			body {
				font-family: sans-serif;
			}
			div {
				font-weight: bold;
			}
		</style>
		<div class="container">
			<h1>Rock Paper Scissors</h1>
			<p>Welcome: <?= htmlentities($username) ?></p>
			<form method="post">
				<p><label for="select"></label>
					<select name="pick" id="select">
						<option value="" >Select</option>
						<option value="Rock">Rock</option>
						<option value="Paper">Paper</option>
						<option value="Scissors">Scissors</option>
						<option value="test">Test</option>
					</select>
					<input type="submit" name = "play" value="Play" />
					<input type="button" name="logout" value="Log out"/>
				</p>
			</form>
			<div style="background-color: lightgrey; border-radius: 5px; width: 550px; padding: 1px 5px;">
				<?php
				   if ( $game != false )  {
				        echo("<p>$game</p>\n");
				    }
				?>
			</div>
		</div>
	</body>

</html>