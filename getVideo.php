<?php
require 'db.php';

if(isset($_GET['query'])) {
   $requser = $bdd->prepare("SELECT * FROM videos WHERE videoID = ?");
	 $requser->execute(array($_GET['query']));
	 $userexist = $requser->rowCount();
	 if($userexist == 1) {
		$userinfo = $requser->fetch(PDO::FETCH_ASSOC);
		echo json_encode($userinfo);
	}
	else {
		echo "video doesn't exist";
	}
}
?>