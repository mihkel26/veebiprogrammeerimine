<?php 
	$database = "if17_magimihk";
	require("../../../config.php");
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
	
	function tabel(){
		$notice = "";
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		$stmt = $mysqli->prepare("SELECT firstname, lastname, email, gender, birthday FROM vpusers");
		echo $mysqli->error;
		$stmt->bind_result($firstname, $lastname, $email, $gender, $birthday);
		$stmt-> execute();
		while($stmt->fetch()){
			$notice .= "<tr><td>".$firstname ."</td><td>".$lastname ."</td><td>".$email ."</td><td>".$gender ."</td><td>".$birthday ."</td></tr>";
			}
		$stmt->close();
		$mysqli->close();
		return $notice;
	}
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
		$stmt -> bind_param("iss", $_SESSION["userId"], $idea, $color);
		if($stmt-> execute()){
			$notice = "Mõte on salvestatud!";
		}	else {
			$notice = "Salvestamisel tekkis viga! " .$stmt->error;
		}
		
		$stmt->close();
		$mysqli->close();
		return $notice;
	}
	#ideelist
	function listIdeas(){
		$notice = "";
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		#$stmt = $mysqli -> prepare("SELECT idea, ideacolor from userideas");
		#$stmt = $mysqli -> prepare("SELECT idea, ideacolor from userideas order by id desc");
		$stmt = $mysqli -> prepare("SELECT id, idea, ideacolor from userideas where userid = ? and deleted IS NULL order by id desc");
		echo $mysqli->error;
		$stmt->bind_param("i", $_SESSION["userId"]);
		$stmt->bind_result($id, $idea, $color);
		$stmt-> execute();
		
		while($stmt->fetch()){
			#<p> style="background-color: #ff5567;"> HEA MÕTE </p>
			#$notice .= '<p style="background-color: ' .$color .'">' .$idea . "</p> \n"; 
			#<p> style="background-color: #ff5567;"> HEA MÕTE | <a href="edituseridea.php?id=34">Toimeta</a> </p>
			$notice .= '<p style="background-color: ' .$color .'">' .$idea .' | <a href="editusersidea.php?id=' .$id . '">Toimeta</a>'. "</p> \n";
			
		}
		
		$stmt->close();
		$mysqli->close();
		return $notice;
	}
	#viimane idee (avalik)
	function latestIdea(){
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		$stmt = $mysqli->prepare("SELECT idea from userideas where id = (select max(id) from userideas)");
		$stmt->bind_result ($idea);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();
		$mysqli->close();
		return $idea;
	}
	
	function vpphotos($filename, $thumbnail, $visibility){
		$notice = " ";
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		$stmt = $mysqli -> prepare("INSERT INTO vpphotos (userid, filename, thumbnail, visibility) VALUES (?, ?, ?, ?)");
		echo $mysqli->error;
		$stmt -> bind_param("issi", $_SESSION["userId"], $filename, $thumbnail, $visibility);
		if($stmt-> execute()){
			$notice = "Kõik timm!";
		}	else {
			$notice = "Salvestamisel tekkis viga! " .$stmt->error;
		}
		
		$stmt->close();
		$mysqli->close();
		return $notice;
	}
	
	function publicPhotos(){
		$notice = "";
		$picDir = "../../thumbs/";
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		$stmt = $mysqli->prepare("SELECT thumbnail from vpphotos where visibility = 1 order by id DESC limit 10");
		echo $mysqli->error;
		$stmt->bind_result ($thumbnailName);
		$stmt->execute();
		
		while($stmt->fetch()){
			$notice .=  '<img src="' . $picDir . '/' . $thumbnailName . '" alt="Auto">';
			
		}
		
		$stmt->close();
		$mysqli->close();
		return $notice;
	}
	
	function userPhotos(){
		$notice = "";
		$picDir = "../../uploadpics/";
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		$stmt = $mysqli->prepare("SELECT filename from vpphotos where userid = ? order by id DESC limit 5");
		echo $mysqli->error;
		$stmt->bind_param("i", $_SESSION["userId"]);
		$stmt->bind_result ($fileName);
		$stmt->execute();
		
		while($stmt->fetch()){
			$notice .=  '<img src="' . $picDir . '/' . $fileName . '" alt="Auto">';
			
		}
		
		$stmt->close();
		$mysqli->close();
		return $notice;
	}
	
	function usersPhotos(){
		$notice = "";
		$picDir = "../../uploadpics/";
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		$stmt = $mysqli->prepare("SELECT filename from vpphotos where userid != ? and visibility != 3 order by id DESC");
		echo $mysqli->error;
		$stmt->bind_param("i", $_SESSION["userId"]);
		$stmt->bind_result ($fileName);
		$stmt->execute();
		
		while($stmt->fetch()){
			$notice .=  '<img src="' . $picDir . '/' . $fileName . '" alt="Auto">';
			
		}
		
		$stmt->close();
		$mysqli->close();
		return $notice;
	}
	
	/*function publicPhotos(){
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		$stmt = $mysqli->prepare("SELECT thumbnail from vpphotos where visibility = 1");
		$stmt->bind_result ($thumbnailName);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();
		$mysqli->close();
		return $thumbnailName;
	}
	*/
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