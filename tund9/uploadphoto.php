<?php
	//et pääseks ligi sessioonile ja funktsioonidele
	require("functions.php");
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
	
	$allFiles = array_slice(scandir($picDir), 2);
	foreach ($allFiles as $file){
		$fileType = pathinfo($file, PATHINFO_EXTENSION);
		if (in_array($fileType, $picFileTypes) == true){
			array_push($picFiles, $file);
			
		}
			
	}
	
	//$allFiles = scandir($picDir);
	//var_dump($allFiles);
	//$picFiles = array_slice($allFiles, 2);
	//var_dump($picFiles);
	$picFileCount = count ($picFiles);
	$picNumber = mt_rand(0, $picFileCount - 1);
	$picFile = $picFiles [$picNumber];
	
	//pildi alla laadimine
	$target_dir = "../../uploadpics/";
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
			$target_file = $target_dir .pathinfo(basename($_FILES["fileToUpload"]["name"]))["filename"] ."_" .(microtime(1) * 10000) ."." .$imageFileType;
		
		if(!empty($_FILES["fileToUpload"]["name"])){
			
			//$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
			$imageFileType = strtolower(pathinfo(basename($_FILES["fileToUpload"]["name"]))["extension"]);
			$timeStamp = microtime(1) *10000;
			$target_file = $target_dir . pathinfo(basename($_FILES["fileToUpload"]["name"]))["filename"] ."_" .$timeStamp ."." .$imageFileType;
		
			$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
			if($check !== false) {
				$notice .= "Fail on pilt - " . $check["mime"] . ". ";
				$uploadOk = 1;
			} else {
				$notice .= "See pole pildifail. ";
				$uploadOk = 0;
			}
	}
	
	
			// Kontrollin, kas fail on juba olemas
			if (file_exists($target_file)) {
				$notice .= "Vabandust, fail on juba olemas.";
				$uploadOk = 0;
			}
			
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
			/*if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
				$notice .= "Fail: ". basename( $_FILES["fileToUpload"]["name"]). " on üles laetud!";
			} else {
				$notice .= "Vabandust, faili üles laadimisel tekkis viga";
			}*/
			
			
			
			#Sõltuvalt failitüübist, loon objekti
			
			if($imageFileType == "jpg" or $imageFileType == "jpeg"){
				$myTempImage = imagecreatefromjpeg($_FILES["fileToUpload"]["tmp_name"]);
			}
			
			if($imageFileType == "png"){
				$myTempImage = imagecreatefrompng($_FILES["fileToUpload"]["tmp_name"]);
			}
			
			if($imageFileType == "gif"){
				$myTempImage = imagecreatefromgif($_FILES["fileToUpload"]["tmp_name"]);
			}
			
			#suuruse muutmine
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
			
			#watermark
			$stamp = imagecreatefrompng("../../graphics/hmv_logo.png");
			$stampWidth = imagesx($stamp);
			$stampHeight = imagesy($stamp);
			$stampX = imagesx($myImage) - $stampWidth - $marginHor;
			$stampY = imagesy($myImage) - $stampHeight - $marginVer;
			imagecopy($myImage, $stamp, $stampX, $stampY, 0, 0, $stampWidth, $stampHeight);
			
			#Teksti watermark
			$textToImage = "Nurgatagune Fotopunkt OÜ";
			#värv
			$textColor = imagecolorallocatealpha($myImage, 255, 255, 0, 69); #alpha 0-127
			#mis pildile, suurus, nurk, x, y, cärv, font, txt
			imagettftext($myImage, 35, -30, 30, 50, $textColor,"../../graphics/ARIALBD.TTF", $textToImage);
			
			#salvestame pildi
			if($imageFileType == "jpg" or $imageFileType == "jpeg"){
				if(imagejpeg($myImage, $target_file, 90)){
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
			}
			
			if($imageFileType == "gif" or $imageFileType == "gif"){
				if(imagegif($myImage, $target_file)){
					$notice .= "Fail: ". basename( $_FILES["fileToUpload"]["name"]). " on üles laetud!"; 
				} else {
					$notice .= "Tekkis tõrge!";
				}
			}
				
				#vabastan mälu
				imagedestroy($myTempImage);
				imagedestroy($myImage);
				imagedestroy($stamp);
				
			
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
    <input type="submit" value="Lae pilt üles" name="submit">
</form>
	<span><?php echo $notice; ?></span>
	

</div>
	

</body>
</html>