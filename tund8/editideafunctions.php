<?php
$database = "if17_magimihk";
	require("../../../config.php");
	
	function getSingleIdea($editid){
		$notice = "";
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		$stmt = $mysqli -> prepare("SELECT idea, ideacolor from userideas where id=? ");
		echo $mysqli->error;
		$stmt->bind_param("i", $editid);
		$stmt->bind_result($idea, $color);
		$stmt->execute();
		$ideaObject = new Stdclass();
		if($stmt->fetch()){
			$ideaObject->text = $idea;
			$ideaObject->color = $color;
		} else {
			#Sellist mõtet polnudki
			$stmt->close();
		    $mysqli->close();
			header("Location: usersideas.php");
			exit();
		}
		
		$stmt->close();
		$mysqli->close();
		return $ideaObject;
	}
	
	function updateIdea($id, $idea, $color){
		$notice = "";
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		$stmt = $mysqli -> prepare("update userideas SET idea=?, ideacolor=? where id=?");
		echo $mysqli->error;
		$stmt->bind_param("ssi", $idea, $color, $id);
		$stmt->execute();
		
		$stmt->close();
		$mysqli->close();
	}
	
	function deleteIdea($id){
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		$stmt = $mysqli->prepare("update userideas set deleted=NOW() where id=?");
		echo $mysqli->error;
		$stmt->bind_param("i", $id);
		$stmt->execute();
		
		$stmt->close();
		$mysqli->close();
	}
?>