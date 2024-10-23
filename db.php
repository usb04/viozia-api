<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

$bdd = new PDO(
	'mysql:host=localhost;dbname=viozia;charset=utf8',
	'root',
	'password'
);

?>