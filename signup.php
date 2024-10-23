<?php
require 'db.php';

function getRandomString($n) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$randomString = '';
  
	for ($i = 0; $i < $n; $i++) {
		$index = rand(0, strlen($characters) - 1);
		$randomString .= $characters[$index];
	}
  
	return $randomString;
}

function guidv4($data)
{
	assert(strlen($data) == 16);

	$data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
	$data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

	return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

if(isset($_POST['email'])) {
   $username = htmlspecialchars($_POST['username']);
   $email = htmlspecialchars($_POST['email']);
   $email2 = htmlspecialchars($_POST['email']);
   $password = md5($_POST['password']);
   $password2 = md5($_POST['password']);
   if(!empty($_POST['username']) AND !empty($_POST['email']) AND !empty($_POST['email']) AND !empty($_POST['password']) AND !empty($_POST['password'])) {
	  $usernamelength = strlen($username);
	  if($usernamelength <= 255) {
		 if($email == $email2) {
			if(1==1) {
			   $reqemail = $bdd->prepare("SELECT * FROM membres WHERE email = ?");
			   $reqemail->execute(array($email));
			   $emailexist = $reqemail->rowCount();
			   if($emailexist == 0) {
				  if($password == $password2) {
					  $userid = guidv4(openssl_random_pseudo_bytes(16));
					  $useruuid = guidv4(openssl_random_pseudo_bytes(16));
					  
					 $insertmbr = $bdd->prepare("INSERT INTO users(username, userid, useruuid, email, password) VALUES(?, ?, ?, ?, ?)");
					 $insertmbr->execute(array($username, $useruuid, $userid, $email, $password));
					 
					 $requser = $bdd->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
					   $requser->execute(array($email, $password));
					   $userexist = $requser->rowCount(PDO::FETCH_ASSOC);
					   if($userexist == 1) {
						  $userinfo = $requser->fetch();
						   echo json_encode($userinfo);
					  }
					  else {
						  $erreur = "Something went wrong.";
						  $reason = "Server error.";
						  $res = [
							  "error" => $erreur,
							  "reason" => $reason
						  ];
						  echo json_encode($res);
					  }
				  } else {
					 $erreur = "Something went wrong.";
					 $res = [
						 "error" => $erreur
					 ];
					 echo json_encode($res);
				  }
			   } else {
				  $erreur = "Account already exists.";
				  $res = [
					  "error" => $erreur
				  ];
				  echo json_encode($res);
			   }
			} else {
			   $erreur = "Wrong e-mail.";
			   $res = [
				   "error" => $erreur
			   ];
			   echo json_encode($res);
			}
		 } else {
			$erreur = "Something went wrong.";
			$res = [
				"error" => $erreur
			];
			echo json_encode($res);
		 }
	  } else {
		 $erreur = "Username too long.";
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