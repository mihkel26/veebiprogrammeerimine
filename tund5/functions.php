<?php 
	$database = "if17_magimihk";
	
	# Uue kasutaja andmebaasi lisamine
	function signup($signupFirstName, $signupFamilyName, $signupBirthDate, $gender, $signupEmail, $signupPassword){
		# ühendus serveriga
		
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		#Käsk serverile
		$stmt = $mysqli -> prepare("INSERT INTO vpusers (firstname, lastname, birthday, gender, email, password) VALUES (?, ?, ?, ? ,? ,?)");
		echo $mysqli->error;
		## s - string
		## i - INT 
		## d - DEC, ujukomaarv
		$stmt->bind_param("sssiss", $signupFirstName, $signupFamilyName, $signupBirthDate, $gender, $signupEmail, $signupPassword);
		//$stmt->execute();
		if($stmt->execute()){
			echo "Läks väga hästi!";
		} else {
			echo "Tekkis viga: " .$stmt->error;
		}
		$stmt->close();
		$mysqli->close();
	}
	
	#Sisestuse kontrollimine
	function test_input($data){
		$data = trim($data); #eemaldab lõpust tühiku
		$data = stripslashes($data); #eemaldab "\"
		$data = htmlspecialchars($data); #eemaldab keelatud märgid
		return $data;
	}
	
	/* $x = 8;
	$y = 5;
	echo "Esimene summa on: " .($x + $y);
	addValues();
	
	function addValues(){
		echo "Teine summa on: " .($GLOBALS["x"] + $GLOBALS["y"]);
		$a = 4;
		$b = 1;
		echo "Kolmas summa on: " .($a + $b);
		return
	}
	
	echo "Neljas summa on: " .($a + $b); */
?>