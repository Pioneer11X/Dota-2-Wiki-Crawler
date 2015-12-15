<?php

include "helpers.php";

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
$xpath_query['heroPortrait'] = '//*[@id="heroTopPortraitContainer"]//img';

foreach ( $hero_names as $hero ){

	$hero_url = $base_url . $hero . "/";

	$content = get_content($hero_url);
	$dom = new DOMDocument();
	@$dom->loadHTML($content);
	$xPath = new DOMXPath($dom);

	$abilitiesArray = array();

	echo "Hero Name: $hero\n";
	echo "Hero Url: $hero_url\n";

	$abilities = $xPath->query($xpath_query['abilities']);
	$portraitUrl = get_image_url($xPath);

	echo "portrait Url: $portraitUrl\n";
	echo "\n";
	foreach ( $abilities as $ability ){
		$ability_string = $xPath->evaluate($xpath_query['abilityName'],$ability);
		$abilitiesArray[] = $ability_string->item(0)->nodeValue;
	}
	$outputArray[$hero]['abilityNames'] = $abilitiesArray;
}

$outputJson = json_encode($outputArray);

$fp = fopen("heroes.json","w");
$retVal = fwrite($fp,$outputJson);
fclose($fp);

function get_image_url($xPath){

	global $xpath_query;

	$imageNode = $xPath->query($xpath_query['heroPortrait']);
	$imageUrl = $imageNode->item(0)->getAttribute("src");

	return $imageUrl;
}

?>
