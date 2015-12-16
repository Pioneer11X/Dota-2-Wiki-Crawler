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
		$abilityArray[$abilityNameString] = array($abilityDescriptionString); // We can use this array shit to add other things like Mana Cost and numbers
	}
	$outputArray[$hero]['abilities'] = $abilityArray;
}

$outputJson = json_encode($outputArray);

$fp = fopen(DOCUMENT_ROOT ."resources/heroes.json","w");
$retVal = fwrite($fp,$outputJson);
fclose($fp);

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

?>
