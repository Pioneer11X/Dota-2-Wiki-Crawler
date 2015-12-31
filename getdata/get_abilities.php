<?php

$fp = file_get_contents("../resources/heroes.json","r");
$newFile = "../resources/abilities.json";

$jsonArray = json_decode($fp,true);
$abilitiesArray = array();

foreach ( $jsonArray as $heroName=>$data ){
	$abilitiesArray[$heroName] = $data["abilities"];
}

$jsonString = json_encode($abilitiesArray);
$jsonString = str_replace(array("{","}"),array("[","]"),$jsonString);

file_put_contents($newFile,$jsonString);

//echo $jsonArray["Axe"]["bio"];

?>
