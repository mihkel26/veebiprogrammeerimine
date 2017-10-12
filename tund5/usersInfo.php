<?php
	#Et pääseks ligi sessile ja funktsioonidele
	require("functions.php");
	
	#Kui pole sisse loginud, liigume login lehele
	if(!isset($_SESSION["userId"])){
		header("Location: login.php");
		exit();
	}
	
	if(isset($_GET["logout"])){
		session_destroy();
		header("Location: login.php");
		exit();
	}
	
	//muutujad
	$myName = "Andrus";
	$myFamilyName = "Rinde";

	
	#while($stmt->fetch()){
	#	#read, mis loovad iga kasutaja kohta tabeli rea
	#}
	
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>
		Andrus Rinde programmeerib veebi
	</title>
</head>
<body>
	<h1>Tere, <?php echo $_SESSION["firstname"] ." " .$_SESSION["lastname"]; ?></h1>
	<p>See veebileht on loodud õppetöö raames ning ei sisalda mingisugust tõsiseltvõetavat sisu!</p>
	
	<table border="1" style="border: 1px solid black; border-collapse: collapse">
	<tr>
		<th>Eesnimi</th><th>perekonnanimi</th><th>e-posti aadress</th><th>sünnipäev</th><th>sugu</th>
	</tr>
	<?php echo $tableresult ?>
	</table>
	<p><a href="?logout=1">Logi välja!</a></p>
	
</body>
</html>