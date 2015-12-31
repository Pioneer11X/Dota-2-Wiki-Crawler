<?php

$fp = file_get_contents("../resources/heroes.json","r");
$abilityNamesFile = "../resources/abilityNames.json";
$abilityDescriptionFile = "../resources/abilityDescription.json";

$jsonArray = json_decode($fp,true);
$abilityNameArray = array();
$abilityDescriptionArray = array();

foreach ( $jsonArray as $heroName=>$data ){

	$abilityNameArray[$heroName] = array();
	foreach ( $data["abilities"] as $abilityName=>$descriptionArray ){
		$abilityNameArray[$heroName][] = $abilityName;
		$abilityDescriptionArray[$heroName][] = $descriptionArray["Description"][0];
	}
}

$jsonString = json_encode($abilityNameArray);
$jsonString = str_replace(array("{","}","\\"),array("[","]",""),$jsonString);

file_put_contents($abilityNamesFile,$jsonString);

$jsonString = json_encode($abilityDescriptionArray);
$jsonString = str_replace(array("{","}","\\"),array("[","]",""),$jsonString);

file_put_contents($abilityDescriptionFile,$jsonString);

//echo $jsonArray["Axe"]["bio"];

?>
