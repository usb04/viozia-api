<?php 
	require 'db.php';
	
	$userid = "";
	
	$requser = $bdd->prepare("SELECT * FROM users WHERE useruuid = ?");
	  $requser->execute(array($_POST['userid']));
	  $userexist = $requser->rowCount();
	  if($userexist == 1) {
		 $userinfo = $requser->fetch();
		 $userid = $userinfo['userid'];
		 
	
	$currentDirectory = getcwd();
	$uploadDirectory = "/uploads/";
	$uid = uniqid();
	$minUploadDirectory = $uploadDirectory;
	$uploadDirectory = $uploadDirectory . $uid;
	
	$errors = []; // Store errors here
	
	$fileExtensionsAllowed = ['mp4','webm','mov', '3gp', 'wmv', 'mkv']; // These will be the only file extensions allowed 
	
	$fileName = $_FILES['video']['name'];
	$fileSize = $_FILES['video']['size'];
	$fileTmpName  = $_FILES['video']['tmp_name'];
	$fileType = $_FILES['video']['type'];
	$tmp = explode('.', $fileTmpName);
	$fileExtension = end($tmp);
	$newFile = $uid . "." . $fileExtension;
	
	$uploadPath = $currentDirectory . $uploadDirectory . basename($fileName); 
	
	if (isset($userid)) {
	
	  if ($fileSize > 200000000) {
			$erreur = "File over 200MB";
			$res = [
			  "error" => $erreur
		  ];
		  echo json_encode($res);
	  }
	
	  if (empty($errors)) {
		$didUpload = move_uploaded_file($fileTmpName, $uploadPath);
	
		if ($didUpload) {
			$fileURL = "https://developeurs.viozia.fr/api" . $uploadDirectory . $fileName;
		  	
			  $newFileName720 = "uploads/viozia_720_".$uid.".webm";
			  $newFileURL720 = "https://developeurs.viozia.fr/api/" . $newFileName720;
			  
			  $newFileName480 = "uploads/viozia_480_".$uid.".webm";
			  $newFileURL480 = "https://developeurs.viozia.fr/api/" . $newFileName480;
			  
			  $file720 = [
					"fileName" => $newFileName720,
					"url" => $newFileURL720,
			  ];
			  
			  $file480 = [
					  "fileName" => $newFileName480,
					  "url" => $newFileURL480,
				];
			  
			  $filesArray = [
				  "480p" => $file480,
				  "720p" => $file720,
			  ];
			  
			  $thname = "uploads/viozia_thumbnail_".$uid.".jpg";
			  $th = "https://developeurs.viozia.fr/api/" . $thname;
			  
			  $res = [
				"success" => $didUpload,
				"files" => $filesArray,
				"videoID" => $uid,
				"thumbnail" => $th
			];
			
			shell_exec("ffmpeg -i ".$currentDirectory.$uploadDirectory.$fileName." -vf scale=-1:720 -vcodec libvpx -cpu-used -5 -deadline realtime -b:v 1.5M -c:a libvorbis uploads/viozia_720_".$uid.".webm");
			
			shell_exec("ffmpeg -i ".$currentDirectory.$uploadDirectory.$fileName.' -vf scale=-1:480 -vcodec libvpx -cpu-used -5 -deadline realtime -b:v 0.3M -c:a libvorbis uploads/viozia_480_'.$uid.".webm");
			
			shell_exec("ffmpeg -i ".$currentDirectory.$uploadDirectory.$fileName.' -vf "select=eq(n\,42)" -vframes 1 uploads/viozia_thumbnail_'.$uid.".jpg");
			
			unlink($currentDirectory.$uploadDirectory.$fileName);
			
			echo json_encode($res);
			
			$desc = "My new video !";
			if(isset($_POST['description'])) {
				$desc = $_POST['description'];
			}
			
			$insertmbr = $bdd->prepare("INSERT INTO videos(title, userid, video720, video480, videoID, description, thumbnailURL) VALUES(?, ?, ?, ?, ?, ?, ?)");
			$insertmbr->execute(array($_POST['title'], $userid, $newFileURL720, $newFileURL480, $uid, $desc, $th));
			
			
		} else {
		  $erreur = "Something went wrong.";
		  $res = [
				"error" => $erreur
			];
			echo json_encode($res);
		}
	  } else {
		foreach ($errors as $error) {
		  echo $error . "These are the errors" . "\n";
		}
	  }
	
	}
}
?>