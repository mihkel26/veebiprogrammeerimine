<?php
	//et pääseks ligi sessioonile ja funktsioonidele
	require("functions.php");
	require("classes/Uploadphoto.class.php");
	$notice = "";
	
	//kui pole sisseloginud, liigume login lehele
	if(!isset($_SESSION["userId"])){
		header("Location: login.php");
		exit();
	}
	// kui logib välja
	if(isset($_GET["logout"])){
		session_destroy();
		header("Location: login.php");
		exit();
	}

	
	
	$picDir = "../../uploadpics/";
	$picFiles = [];
	$picFileTypes = ["jpg", "jpeg", "png", "gif", "jfif",];
	$visibility = "";
	
	
	/*klassi esimene näide
	$esimene = new Uploadphoto("Kaval trikk. ");
	echo $esimene->testPublic;
	$teine = new Uploadphoto ("Ja nii juba 2 korda. ");
	*/
	
	//pildi alla laadimine
	$target_dir = "../../uploadpics/";
	$target_dir2 = "../../thumbs/";
	$target_file = "";
	$uploadOk = 1;
	$maxWidth = 600;
	$maxHeight = 400;
	$marginHor = 10;
	$marginVer = 10;
	
	
	// Kas vajutati laadimise nuppu
	if(isset($_POST["submit"])) {
		//kas fail on valitud, failinimi olemas
		if(!empty($_FILES["fileToUpload"]["name"])){
			
			//fikseerin failinime
			$imageFileType = strtolower(pathinfo(basename($_FILES["fileToUpload"]["name"]),PATHINFO_EXTENSION));
			//$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
			/*$target_file = $target_dir . "hmv_" .(microtime(1) * 10000) ."." .$imageFileType;
			$target_file2 = $target_dir2 ."hmv_" .(microtime(1) * 10000) ."." .$imageFileType;
			*/
			$target_file ="hmv_" .(microtime(1) * 10000) ."." .$imageFileType;
			$target_file2 ="hmv_" .(microtime(1) * 10000) ."." .$imageFileType;
			
			$filename = "hmv_" .(microtime(1) * 10000) ."." .$imageFileType;
			$thumbnail = "hmv_" .(microtime(1) * 10000) ."." .$imageFileType;
		
		if(!empty($_FILES["fileToUpload"]["name"])){
			
			//$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
			$imageFileType = strtolower(pathinfo(basename($_FILES["fileToUpload"]["name"]))["extension"]);
			$timeStamp = microtime(1) *10000;
			$target_file = "hmv_" .$timeStamp ."." .$imageFileType;
		
			$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
			if($check !== false) {
				$notice .= "Fail on pilt - " . $check["mime"] . ". ";
				$uploadOk = 1;
			} else {
				$notice .= "See pole pildifail. ";
				$uploadOk = 0;
			}
	}
	
	
			/* Kontrollin, kas fail on juba olemas
			if (file_exists($target_file)) {
				$notice .= "Vabandust, fail on juba olemas.";
				$uploadOk = 0;
			}*/
			
			
			// Piiran faili suurust
			if ($_FILES["fileToUpload"]["size"] > 1000000) {
						$notice .= "Pilt on liiga suur! ";
						$uploadOk = 0;
					}
			
			// Lubame ainult kindlaid failitüüpe
			if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
			&& $imageFileType != "gif" ) {
				$notice .= "Vabandust, ainult jpg, jpeg, png & gif formaadid on lubatud.";
				$uploadOk = 0;
			}
			
			// Kontrollime, kas $uploadOk on pandud kogemata 0
			if ($uploadOk == 0) {
				$notice .= "Vabandame, antud faili ei laetud üles.";
				// Kui kõik on korras, proovime faili üles laadida.
			} else {
				$myPhoto = new Uploadphoto($_FILES["fileToUpload"]["tmp_name"], $imageFileType);
				$myPhoto -> resizePhoto($maxWidth, $maxHeight);
				$myPhoto -> addWatermark( "../../graphics/hmv_logo.png", $marginHor, $marginVer);
				$myPhoto -> addTextWatermark("Pildipealne txt");
				$notice = $myPhoto -> savePhoto($target_dir, $target_file);
				#$myPhoto -> saveOriginal($target_dir, $target_file)
				$myPhoto -> clearImages();
				
				unset($myPhoto);
				
				
				
				
				
			/*if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
				$notice .= "Fail: ". basename( $_FILES["fileToUpload"]["name"]). " on üles laetud!";
			} else {
				$notice .= "Vabandust, faili üles laadimisel tekkis viga";
			}*/
			
			
			
			#Sõltuvalt failitüübist, loon objekti
			
			/*if($imageFileType == "jpg" or $imageFileType == "jpeg"){
				$myTempImage = imagecreatefromjpeg($_FILES["fileToUpload"]["tmp_name"]);
			}
			
			if($imageFileType == "png"){
				$myTempImage = imagecreatefrompng($_FILES["fileToUpload"]["tmp_name"]);
			}
			
			if($imageFileType == "gif"){
				$myTempImage = imagecreatefromgif($_FILES["fileToUpload"]["tmp_name"]);
			}*/
			
			/*suuruse muutmine
			#teeme kindlaks suuruse
			$imageWidth = imagesx($myTempImage);
			$imageHeight = imagesy($myTempImage);
			#arvutan õige suuruse
			if($imageWidth > $imageHeight) {
				$sizeRatio = $imageWidth / $maxWidth;
			} else {
				$sizeRatio = $imageHeight / $maxHeight;
			}
			#tekitame uue sobiva pikslikogumi
			$myImage = resizeImage ($myTempImage, $imageWidth, $imageHeight, round($imageWidth/ $sizeRatio), round($imageHeight / $sizeRatio));
			$myImage2 = resizeImage ($myTempImage, $imageWidth, $imageHeight, 100, 100);*/
			
			/*#watermark
			$stamp = imagecreatefrompng("../../graphics/hmv_logo.png");
			$stampWidth = imagesx($stamp);
			$stampHeight = imagesy($stamp);
			$stampX = imagesx($myImage) - $stampWidth - $marginHor;
			$stampY = imagesy($myImage) - $stampHeight - $marginVer;
			imagecopy($myImage, $stamp, $stampX, $stampY, 0, 0, $stampWidth, $stampHeight);*/
			
			/*#Teksti watermark
			$textToImage = "Nurgatagune Fotopunkt OÜ";
			#värv
			$textColor = imagecolorallocatealpha($myImage, 255, 255, 0, 69); #alpha 0-127
			#mis pildile, suurus, nurk, x, y, cärv, font, txt
			imagettftext($myImage, 35, -30, 30, 50, $textColor,"../../graphics/ARIALBD.TTF", $textToImage);
			*/
			
			/*
			#salvestame pildi
			if($imageFileType == "jpg" or $imageFileType == "jpeg"){
				if(imagejpeg($myImage, $target_file, 90)){
					$notice .= "Fail: ". basename( $_FILES["fileToUpload"]["name"]). " on üles laetud!"; 
				} else {
					$notice .= "Tekkis tõrge!";
				}
				if(imagejpeg($myImage2, $target_file2, 90)){
					$notice .= "Fail: ". basename( $_FILES["fileToUpload"]["name"]). " on üles laetud!"; 
				} else {
					$notice .= "Tekkis tõrge!";
				}
			}
			
			if($imageFileType == "png" or $imageFileType == "png"){
				if(imagepng($myImage, $target_file, 5)){
					$notice .= "Fail: ". basename( $_FILES["fileToUpload"]["name"]). " on üles laetud!"; 
				} else {
					$notice .= "Tekkis tõrge!";
				}
				if(imagejpeg($myImage2, $target_file2, 90)){
					$notice .= "Fail: ". basename( $_FILES["fileToUpload"]["name"]). " on üles laetud!"; 
				} else {
					$notice .= "Tekkis tõrge!";
				}
			}
			
			if($imageFileType == "gif" or $imageFileType == "gif"){
				if(imagegif($myImage, $target_file)){
					$notice .= "Fail: ". basename( $_FILES["fileToUpload"]["name"]). " on üles laetud!"; 
				} else {
					$notice .= "Tekkis tõrge!";
				}
				if(imagejpeg($myImage2, $target_file2, 90)){
					$notice .= "Fail: ". basename( $_FILES["fileToUpload"]["name"]). " on üles laetud!"; 
				} else {
					$notice .= "Tekkis tõrge!";
				}
			}
			*/
			if (isset($_POST["visibility"]) && !empty($_POST["visibility"])){ //kui on määratud ja pole tühi
			$visibility = intval($_POST["visibility"]);
			} else {
				
			}
			
			vpphotos($filename, $thumbnail, $visibility);
				
				/*
				#vabastan mälu
				imagedestroy($myTempImage);
				imagedestroy($myImage);
				imagedestroy($stamp);*/
				
			
			} #kas saab salvestada
		} else { //kas fail on valitud, failinimi olemas lõppeb
				$notice = "Palun valige kõigepealt pildifail!";
			}
	} // if submit lõppeb
	
	function resizeImage($image, $origW, $origH, $w, $h){
		$newImage = imagecreatetruecolor($w, $h);
		#Kuhu, kust, kuhu koordinaatidele x, y ja kust koordinaatidelt x ja y ja kui laialt ja kõrgelt uude kohta, kui laialt ja kõrgelt võtta
		imagecopyresampled($newImage, $image, 0, 0, 0, 0, $w, $h, $origW, $origH);
		return $newImage;
	}
	/*function resizeImage2($image, $origW, $origH, $w, $h){
		$newImage2 = imagecreatetruecolor(100, 100);
		#Kuhu, kust, kuhu koordinaatidele x, y ja kust koordinaatidelt x ja y ja kui laialt ja kõrgelt uude kohta, kui laialt ja kõrgelt võtta
		imagecopyresampled($newImage2, $image, 0, 0, 0, 0, 100, 100, $origW, $origH);
		return $newImage2;
	}*/
?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Pildid</title>
	<style>

</style>
</head>
<body>
<div class="container">

<header>
   <h1>veebiprogrammeerimine</h1>
</header>
  

    <p><a href="?logout=1">Logi välja</a></p>
	<p><a href="main.php">Pealeht</a></p>
	<p><a href="usersInfo.php">Kasutajate andmebaas</a></p>
	<p><a href="usersideas.php">Head mõtted</a></p>


<article>
	<h1>Tere, <?php echo $_SESSION["firstname"] ." " .$_SESSION["lastname"]; ?></h1>
	<p>See veebileht on loodud õppetööraames ning ei sisalda mingisugust tõsiseltvõetavat sisu!</p>
	<form action="uploadphoto.php" method="post" enctype="multipart/form-data">
    Valige pildifail, mida soovite üles laadida:
    <input type="file" name="fileToUpload" id="fileToUpload">
	<p>Vali pildi privaatsusaste</p>
	<input type="radio" id="1"
     name="visibility" value="1">
    <label for="1">Avalik Pilt</label>

    <input type="radio" id="2"
     name="visibility" value="2">
    <label for="2">Nähtav sisseloginud kasutajatele</label>

    <input type="radio" id="3"
     name="visibility" value="3" checked="checked">
    <label for="3">Nähtav ainult sulle</label>
	
    <input type="submit" value="Lae pilt üles" name="submit">
	
</form>
	<span><?php echo $notice; ?></span>
	
	

</div>
	

</body>
</html>