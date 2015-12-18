<?php

include "/var/www/sravan/appdata/getdata/settings.php";
include DOCUMENT_ROOT . "getdata/helpers.php";

$hero_list_page = "http://www.dota2.com/heroes/";

// Logic to get the hero list.

$hero_names = array();

$list_crawl = get_content($hero_list_page);

$listDom = new DOMDocument();
@$listDom->loadHTML($list_crawl);
$listXPath = new DOMXPath($listDom);

$list_xpath_query = '//*[@id="filterName"]//option/text()';
$hero_options = $listXPath->query($list_xpath_query);
foreach( $hero_options as $hero_option ){
	$hero_names[] = $hero_option->nodeValue;
}

// We need to remove the first two nodes
if (($key = array_search('HERO NAME', $hero_names)) !== false) {
    unset($hero_names[$key]);
}
if (($key = array_search('All', $hero_names)) !== false) {
    unset($hero_names[$key]);
}

// We now need to add underscores instead of spaces in the names
foreach( $hero_names as $key=>$hero_name ){
	$hero_names[$key] = str_replace(" ", "_", $hero_names[$key] );
	$hero_names[$key] = str_replace("'", "", $hero_names[$key]  );
}


$base_url = "http://www.dota2.com/hero/";

// We need to get the data of all the heroes and save it into a json file for our application to use.
// We then automate the process to run once a day, and update our app accordingly.

$ouputArray = array();

$xpath_query = array();
$xpath_query['abilities'] = '//*[@class="overviewAbilityRowDescription"]';
$xpath_query['abilityName'] = './/h2/text()';
$xpath_query['abilityDescription'] = './/p/text()';
$xpath_query['heroPortrait'] = '//*[@id="heroTopPortraitContainer"]//img';
$xpath_query['heroProfile'] = '//*[@id="heroPrimaryPortraitImg"]';
$xpath_query['heroBio'] = '//*[@id="bioInner"]';
$xpath_query['intVal'] = '//*[@id="overview_IntVal"]';
$xpath_query['strVal'] = '//*[@id="overview_StrVal"]';
$xpath_query['agiVal'] = '//*[@id="overview_AgiVal"]';
$xpath_query['attackVal'] = '//*[@id="overview_AttackVal"]';
$xpath_query['speedVal'] = '//*[@id="overview_SpeedVal"]';
$xpath_query['defenseVal'] = '//*[@id="overview_DefenseVal"]';

foreach ( $hero_names as $hero ){

	$hero_url = $base_url . $hero . "/";

	$content = get_content($hero_url);
	$dom = new DOMDocument();
	@$dom->loadHTML($content);
	$xPath = new DOMXPath($dom);

	$abilityArray = array();

	echo "Hero Name: $hero\n";
	echo "Hero Url: $hero_url\n";

	$abilities = $xPath->query($xpath_query['abilities']);
	$portraitUrl = get_portrait_url($xPath);
	$profileUrl = get_profile_image_url($xPath);

	echo "portrait Url: $portraitUrl\n";
	echo "Profile Url: $profileUrl\n";
	echo "\n";
	foreach ( $abilities as $ability ){
		$abilityNameNode = $xPath->evaluate($xpath_query['abilityName'],$ability);
		$abilityNameString = $abilityNameNode->item(0)->nodeValue;
		$abilityDescriptionNode = $xPath->evaluate($xpath_query['abilityDescription'],$ability);
		$abilityDescriptionString = $abilityDescriptionNode->item(0)->nodeValue;
		$abilityArray[$abilityNameString]['Description'] = array($abilityDescriptionString); // We can use this array shit to add other things like Mana Cost and numbers
	}

	$heroBio = $xPath->query($xpath_query['heroBio']);
	$heroBioString = trim($heroBio->item(0)->nodeValue);

	$stats = array();

	$stats = get_stats($xPath);

	$outputArray[$hero]['abilities'] = $abilityArray;
	$outputArray[$hero]['bio'] = $heroBioString;
	$outputArray[$hero]['stats'] = $stats;
}

$outputJson = json_encode($outputArray);

$fp = fopen(DOCUMENT_ROOT ."resources/heroes.json","w");
$retVal = fwrite($fp,$outputJson);
fclose($fp);

function get_stats($xPath){

	global $xpath_query;

	$attributeArray = array("str","int","agi");

	$stats = array();

	foreach ( $attributeArray as $attribute ){

		$attributeNode = $xPath->query($xpath_query["$attribute"."Val"]);
		$attributeString = $attributeNode->item(0)->nodeValue;
		$attributeExplodedArray = get_exploded_stats_array($attributeString);
		$baseAttribute = $attributeExplodedArray[0];
		$attributeGrowth = $attributeExplodedArray[1];
		$stats[$attribute] = array("base"=>$baseAttribute,"growth"=>"$attributeGrowth");

	}
	
	$attackNode = $xPath->query($xpath_query["attackVal"]);
	$attackString = $attackNode->item(0)->nodeValue;
	$attackStringExplodedArray = explode(" - ",$attackString);
	$baseAttack = $attackStringExplodedArray[0];
	$highAttack = $attackStringExplodedArray[1];

	$stats["baseAttackDamage"] = array("min"=>$baseAttack,"max"=>$highAttack);

	$speedNode = $xPath->query($xpath_query["speedVal"]);
	$speedString = $speedNode->item(0)->nodeValue;

	$stats["baseMoveSpeed"] = $speedString;

	
	$defenseNode = $xPath->query($xpath_query["defenseVal"]);
	$defenseString = $defenseNode->item(0)->nodeValue;

	$stats["baseArmour"] = $defenseString;


	return $stats;

}

function get_portrait_url($xPath){

	global $xpath_query;

	$imageNode = $xPath->query($xpath_query['heroPortrait']);
	$imageUrl = $imageNode->item(0)->getAttribute("src");

	$imageUrl = clean_image_url ( $imageUrl );

	return $imageUrl;
}

function get_profile_image_url ($xPath ){

	global $xpath_query;

	$imageNode = $xPath->query($xpath_query['heroProfile']);
	$imageUrl = $imageNode->item(0)->getAttribute("src");

	$imageUrl = clean_image_url ( $imageUrl );

	return $imageUrl;
}

function get_exploded_stats_array( $string  ){
	$returnArray = explode ( " + " , $string );
	return $returnArray;
}

?>
