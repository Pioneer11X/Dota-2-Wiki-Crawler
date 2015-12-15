<?php

include "helpers.php";

$hero_list_page = "http://www.dota2.com/heroes/";

$list_crawl = get_content($hero_list_page);

$listDom = new DOMDocument();
@$listDom->loadHTML($list_crawl);
$listXPath = new DOMXPath($listDom);

$list_xpath_query = '//*[@id="filterName"]//option/text()';
$hero_options = $listXPath->query($list_xpath_query);



$base_url = "http://www.dota2.com/hero/";

// We need to get the data of all the heroes and save it into a json file for our application to use.
// We then automate the process to run once a day, and update our app accordingly.

// Instead of populating the array like this, we can actually get the names from the option values in the page itself.
$hero_names = array(
		"Earthshaker",
		"Sven",
		"Tiny",
		"Kunkka",
		"Beastmaster",
		"Dragon_Knight",
		"Clockwerk",
		"Omniknight",
		"Huskar",
		"Alchemist",
		"Brewmaster",
		"Treant_Protector",
		"Io",
		"Centaur_Warrunner",
		"Timbersaw",
		"Bristleback",
		"Tusk",
		"Elder_Titan",
		"Legion_Commander",
		"Earth_Spirit",
		"Phoenix"
	);

foreach ( $hero_names as $hero ){

	$content = get_content($base_url.$hero."/");
	$dom = new DOMDocument();
	@$dom->loadHTML($content);
	$xPath = new DOMXPath($dom);

	$xpath_query = array();
	$xpath_query['abilities'] = '//*[@class="overviewAbilityRowDescription"]';
	$xpath_query['abilityName'] = './/h2/text()';

	$abilities = $xPath->query($xpath_query['abilities']);

	echo "Hero Name: $hero\n";
	foreach ( $abilities as $ability ){
		$ability_string = $xPath->evaluate($xpath_query['abilityName'],$ability);
		echo $ability_string->item(0)->nodeValue;
		echo "\n";
	}
	echo "\n";

}


?>
