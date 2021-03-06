<?php 
	$database = "if17_magimihk";
	
	#Alustame sessiooi
	session_start();
	
	#Login func
	function signin($email, $password){
		$notice = "";
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		$stmt = $mysqli->prepare("SELECT id, firstname,lastname, email, gender, birthday, password from vpusers WHERE email = ? ");
		$stmt -> bind_param("s", $email);
		$stmt -> bind_result($id, $firstnameFromDb, $lastnameFromDb, $emailFromDb, $gender, $birthday,$passwordFromDb);
		#$stmt = $mysqli->prepare("SELECT id, firstname, lastname, email, gender, birthday FROM vpusers")
		#$stmt -> bind_param();
		#$stmt -> bind_result($idtbl, $firstnametbl, $lastnametbl, $emailtbl, $gendertbl, $birthdaytbl);
		$stmt -> execute();
	
		
		
		#Kontrollime kasutajat
		if($stmt -> fetch()){
			$hash = hash("sha512", $password);
			if ($hash == $passwordFromDb) {
				$notice = "Kõik korras, logisime sisse!";
				
				#Salvestame sessimuutujaid
				$_SESSION["userId"] = $id;
				$_SESSION["userEmail"] = $emailFromDb;
				$_SESSION["firstname"] = $firstnameFromDb;
				$_SESSION["lastname"] = $lastnameFromDb;
				
				#Liigume pealehele
				header("Location: main.php");
				exit();
			} else {
				$notice = "Sisestasite vale parooli!";
								
			}
		} else {
			$notice = "Sellist kasutajat (".$email .") ei ole!";
		}
		return $notice;
	}
	
	/*tabel
	function tabel(){		
		$stmt = $mysqli->prepare("SELECT id, firstname, lastname, email, gender, birthday FROM vpusers")
		$stmt -> bind_result($id, $firstnametbl, $lastnametbl, $emailtbl, $gendertbl, $birthdaytbl);
		while($stmt->fetch()){
			<tr>
			<td>$firstnametbl</td><td>$lastnametbl</td><td>$emailtbl</td></td>$birthdaytbl</td><td>gendertbl</td>
			</tr>
        }
		return $tableinfo
		
	}
	*/
	function tabel(){
		$notice = "";
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		$stmt = $mysqli->prepare("SELECT id, firstname, lastname, email, gender, birthday FROM vpusers");
		#echo $mysqli->error;
		$stmt->bind_result($id, $firstname, $lastname, $email, $gender, $birthday);
		$stmt-> execute();
		
		while($stmt->fetch()){
			$notice .= "<tr><th>".$firstname ."</th><th>".$lastname ."</th><th>".$email ."</th><th>".$gender ."</th><th>".$birthday ."</th></tr>";
			
		}
		
		$stmt->close();
		$mysqli->close();
		return $notice;
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
	
	function saveIdea($idea, $color){
		$notice = " ";
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		$stmt = $mysqli -> prepare("INSERT INTO userideas (userid, idea, ideacolor) VALUES (?, ?, ?)");
		echo $mysqli->error;
		$stmt -> bind_param("iss", $SESSION["userId"], $idea, $color);
		if($stmt-> execute()){
			$notice = "Mõte on salvestatud!";
		}	else {
			$notice = "Salvestamisel tekkis viga! " .$stmt->error;
		}
		
		$stmt->close();
		$mysqli->close();
		return $notice;
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