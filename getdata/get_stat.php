<?php

$fp = file_get_contents("../resources/heroes.json","r");
$statFile = "../resources/stat.json";

$jsonArray = json_decode($fp,true);
$statArray = array();

foreach ( $jsonArray as $heroName=>$data ){

	$strString = "Str: " . $data["stats"]["str"]["base"] . " + " . $data["stats"]["str"]["growth"] . "\n";
	$agiString = "Agi: " . $data["stats"]["agi"]["base"] . " + " . $data["stats"]["agi"]["growth"] . "\n";
	$intString = "Int: " . $data["stats"]["int"]["base"] . " + " . $data["stats"]["int"]["growth"] . "\n";
	$movString = "Move Speed: " . $data["stats"]["baseMoveSpeed"] . "\n";
	$damString = "Damage: " . $data["stats"]["baseAttackDamage"]["min"] . " - " . $data["stats"]["baseAttackDamage"]["max"] . "\n";
	$armString = "Armour: " . $data["stats"]["baseArmour"] . "\n";

	$statString = $strString . $agiString . $intString . $movString . $damString . $armString;

	$statArray["$heroName"] = $statString;

}

$jsonString = json_encode($statArray);
$jsonString = str_replace(array("{","}"),array("[","]"),$jsonString);

file_put_contents($statFile,$jsonString);

//echo $jsonArray["Axe"]["bio"];

?>
