<?php
require 'db.php';

if(isset($_POST['email'])) {
   $mailconnect = htmlspecialchars($_POST['email']);
   $mdpconnect = md5($_POST['password']);
   if(!empty($mailconnect) AND !empty($mdpconnect)) {
	  $requser = $bdd->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
	  $requser->execute(array($mailconnect, $mdpconnect));
	  $userexist = $requser->rowCount();
	  if($userexist == 1) {
		 $userinfo = $requser->fetch(PDO::FETCH_ASSOC);
		 echo json_encode($userinfo);
	  } else {
		 $erreur = "Wrong credentials.";
		 
		 $res = [
			  "error" => $erreur
		  ];
		  echo json_encode($res);
	  }
   } else {
	  $erreur = "Fill all fields.";
	  
	  	$res = [
			"error" => $erreur
		];
		echo json_encode($res);
   }
}
?>