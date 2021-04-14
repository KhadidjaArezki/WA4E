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

	function validate_edu() {

		for ($i=0; $i <=9 ; $i++) { 
			if (!isset($_POST['year'.$i])) continue;
			if (!isset($_POST['school'.$i])) continue;
			$year = $_POST['year'.$i];
			$desc = $_POST['school'.$i];
			if (strlen($year) == 0 || strlen($desc) == 0) {
				return 'All fields are required';
			}

			if (!is_numeric($year)) {
				return 'Education year must be numeric';
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

	function load_edu($pdo, $profile_id) {
		$stmt = $pdo->prepare('SELECT year, name FROM Education JOIN Institution ON Education.institution_id = Institution.institution_id WHERE profile_id = :prof ORDER BY rank');
		$stmt->execute(array(':prof' => $profile_id));
		$educations = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $educations;
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

	function insertEducations($pdo, $profile_id) {
		$rank = 1;
		for ($i=1; $i <= 9; $i++) { 
			if (!isset($_POST['edu_year'.$i])) continue;
			if (!isset($_POST['school'.$i])) continue;
			$year = $_POST['edu_year'.$i];
			$school = $_POST['school'.$i];

			// Lookup school
			$institution_id = false;
			$stmt = $pdo->prepare('SELECT institution_id FROM Institution WHERE name = :name');
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($row !== false) $institution_id = $row['institution_id'];

			// If there was no institution, insert it
			if ($institution_id === false) {
				$stmt = $pdo->prepare('INSERT INTO Institution (name) VALUES (:name)');
				$stmt->execute(array(':name' => $school));
				$institution_id = $pdo->lastInsertId();
			}

			// Insert education
			$stmt = $pdo->prepare('INSERT INTO Education(profile_id, rank, year, institution_id)VALUES(:pid, :rank, :year, :iid)');
			$stmt->execute(array(
				':pid' => $profile_id,
				':rank' => $rank,
				':year' => $year,
				':iid' => $institution_id));
			$rank++;
		}
	}


 ?>