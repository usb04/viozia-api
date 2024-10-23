<?php
require 'db.php';

$q = $_GET['query'];

$statement = $bdd->prepare('SELECT * FROM videos WHERE concat(title,description) LIKE "%'.$q.'%" LIMIT 5');
$statement->execute();
$results = $statement->fetchAll(PDO::FETCH_ASSOC);
$json = json_encode($results);

echo $json;
?>