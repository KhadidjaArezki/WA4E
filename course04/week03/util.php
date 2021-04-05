<?php 
	function flashMessage() {

		if ( isset($_SESSION['success']) ) {
 			echo('<p style="color: green;">'.$_SESSION['success']."</p>\n");
      		unset($_SESSION['success']);
		}

		if ( isset($_SESSION['error']) ) {
	 		echo('<p style="color: red;">'.$_SESSION['error']."</p>\n");
	      	unset($_SESSION['error']);
		} 
	}

	function validate_profile() {

	 	if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 ||
	 			strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
	 		return "A required field is missing";	 		
	 	}

	 	if (!strpos($_POST['email'], '@')) {
			return 'Email must have an at-sign (@)';
		}
		
		return true;
	}

	function validate_pos() {

		for ($i=0; $i <=9 ; $i++) { 
			if (!isset($_POST['year'.$i])) continue;
			if (!isset($_POST['desc'.$i])) continue;
			$year = $_POST['year'.$i];
			$desc = $_POST['desc'.$i];
			if (strlen($year) == 0 || strlen($desc) == 0) {
				return 'All fields are required';
			}

			if (!is_numeric($year)) {
				return 'Position year must be numeric';
			}
		}
		return true;
	}

	function load_pos($pdo, $profile_id) {
		$stmt = $pdo->prepare("SELECT * FROM Position WHERE profile_id = :pid ORDER BY rank");
		$stmt->execute(array(':pid' => $profile_id));
		$positions = array();
		
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$positions[] = $row;
		}
		return $positions;
	}

	function insert_pos($pdo, $profile_id) {
		$rank = 1;
	    for ($i=0; $i<=9; $i++) { 
	    	if (!isset($_POST["year".$i])) continue;
	    	if (!isset($_POST["desc".$i])) continue;
	    	$year = $_POST["year".$i];
	    	$desc = $_POST["desc".$i];

	    	$stmt = $pdo->prepare('INSERT INTO Position (profile_id, rank, year, description) VALUES (:pid, :rank, :year, :des)');
	    	$stmt->execute(array(
	    		':pid' => $profile_id,
	    		':rank' => $rank,
	    		':year' => $year,
	    		':des' => $desc));
	    	$rank++;
	    }
	}
 ?>