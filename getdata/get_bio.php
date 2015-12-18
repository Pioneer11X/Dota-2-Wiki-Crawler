<?php

$fp = file_get_contents("../resources/heroes.json","r");
$newFile = "../resources/bio.json";

$jsonArray = json_decode($fp,true);
$bioArray = array();

foreach ( $jsonArray as $heroName=>$data ){
	$bioArray[$heroName] = $data["bio"];
}

$jsonString = json_encode($bioArray);

file_put_contents($newFile,$jsonString);

//echo $jsonArray["Axe"]["bio"];

?>
