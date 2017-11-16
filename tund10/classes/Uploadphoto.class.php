<?php
	class Uploadphoto {
		/*private $testPrivate;
		public $testPublic;*/
		
		private $tempFile;
		private $imageFileType;
		private $myTempImage;
		private $myImage;
		private $myImage2;
		
		function __construct($tempFile, $imageFileType){
			/*$this -> testPrivate = $x;
			$this -> testPublic = "Täitsa avalik asi! ";*/
			
			$this -> tempFile = $tempFile;
			$this -> imageFileType = $imageFileType;
		}
		
		private function createImage(){
			if($this -> imageFileType == "jpg" or $this -> imageFileType == "jpeg"){
				$this -> myTempImage = imagecreatefromjpeg($this -> tempFile);
			}
			
			if($this -> imageFileType == "png"){
				$this -> myTempImage = imagecreatefrompng($this -> tempFile);
			}
			
			if($this -> imageFileType == "gif"){
				$this -> myTempImage = imagecreatefromgif($this -> tempFile);
			}
		}
		
		public function resizePhoto($maxWidth, $maxHeight){
			$this -> createImage();
			#suuruse muutmine
			#teeme kindlaks suuruse
			$imageWidth = imagesx($this -> myTempImage);
			$imageHeight = imagesy($this -> myTempImage);
			#arvutan õige suuruse
			if($imageWidth > $imageHeight) {
				$sizeRatio = $imageWidth / $maxWidth;
			} else {
				$sizeRatio = $imageHeight / $maxHeight;
			}
			#tekitame uue sobiva pikslikogumi
			$this -> myImage = $this -> resizeImage ($this -> myTempImage, $imageWidth, $imageHeight, round($imageWidth/ $sizeRatio), round($imageHeight / $sizeRatio));
		}
		
		private function resizeImage($image, $origW, $origH, $w, $h){
		$newImage = imagecreatetruecolor($w, $h);
		imagesavealpha($newImage, true);
		$transColor = imagecolorallocatealpha($newImage, 0, 0, 0, 127);
		imagefill($newImage, 0, 0, $transColor);
		#Kuhu, kust, kuhu koordinaatidele x, y ja kust koordinaatidelt x ja y ja kui laialt ja kõrgelt uude kohta, kui laialt ja kõrgelt võtta
		imagecopyresampled($newImage, $image, 0, 0, 0, 0, $w, $h, $origW, $origH);
		return $newImage;
		}
		
		private function resizeImage2($image, $origW, $origH, $w, $h){
		$newImage = imagecreatetruecolor(100, 100);
		imagesavealpha($newImage, true);
		$transColor = imagecolorallocatealpha($newImage, 0, 0, 0, 127);
		imagefill($newImage, 0, 0, $transColor);
		#Kuhu, kust, kuhu koordinaatidele x, y ja kust koordinaatidelt x ja y ja kui laialt ja kõrgelt uude kohta, kui laialt ja kõrgelt võtta
		imagecopyresampled($newImage, $image, 0, 0, 0, 0, 100, 100, $origW, $origH);
		return $newImage;
		}
		
		public function addWatermark($watermark, $marginHor, $marginVer){
			#addin watermark
			$stamp = imagecreatefrompng($watermark);
			$stampWidth = imagesx($stamp);
			$stampHeight = imagesy($stamp);
			$stampX = imagesx($this -> myImage) - $stampWidth - $marginHor;
			$stampY = imagesy($this -> myImage) - $stampHeight - $marginVer;
			imagecopy($this -> myImage, $stamp, $stampX, $stampY, 0, 0, $stampWidth, $stampHeight);
		}
		
		public function addTextWatermark($text){
			$textColor = imagecolorallocatealpha($this -> myImage, 255, 255, 0, 69); #alpha 0-127
			#mis pildile, suurus, nurk vastupäeva , x, y, cärv, font, txt
			imagettftext($this -> myImage, 35, -30, 30, 50, $textColor,"../../graphics/ARIALBD.TTF", $text);
		}
		
		public function savePhoto($directory, $fileName){
			$target_file = $directory .$fileName;
			$target_file2 = $directory .$fileName;
			#salvestame pildi
			if($this -> imageFileType == "jpg" or $this -> imageFileType == "jpeg"){
				if(imagejpeg($this -> myImage, $target_file, 90)){
					$notice = "Fail on üles laetud!"; 
				} else {
					$notice = "Tekkis tõrge!";
				}
				if(imagejpeg($this -> myImage, $target_file2, 90)){
					$notice = "Fail on üles laetud!"; 
				} else {
					$notice = "Tekkis tõrge!";
				}
			}
			
			if($this -> imageFileType == "png" or $this -> imageFileType == "png"){
				if(imagepng($this -> myImage, $target_file, 5)){
					$notice = "Fail on üles laetud!"; 
				} else {
					$notice = "Tekkis tõrge!";
				}
				if(imagejpeg($this -> myImage2, $target_file2, 90)){
					$notice = "Fail on üles laetud!"; 
				} else {
					$notice = "Tekkis tõrge!";
				}
			}
			
			if($this -> imageFileType == "gif" or $this -> imageFileType == "gif"){
				if(imagegif($this -> myImage, $target_file)){
					$notice = "Fail on üles laetud!"; 
				} else {
					$notice = "Tekkis tõrge!";
				}
				if(imagejpeg($this -> myImage2, $target_file2, 90)){
					$notice = "Fail on üles laetud!"; 
				} else {
					$notice = "Tekkis tõrge!";
				}
			}
			return $notice;
		}
		
		public function saveOriginal($directory, $fileName){
			$target_file = $directory .$fileName;
			if (move_uploaded_file($this -> tempFile, $target_file)) {
				$notice = "Originaalfail on üles laetud!";
			} else {
				$notice = "Vabandust, originaalfaili üles laadimisel tekkis viga!";
			}
			return $notice;
		}
		
		public function clearImages(){
			imagedestroy($this -> myTempImage);
			imagedestroy($this -> myImage);
		}
		
	}	#class lõppeb
?>