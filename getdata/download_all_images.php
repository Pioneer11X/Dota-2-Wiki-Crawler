<?php

include "/var/www/sravan/appdata/getdata/settings.php";
include DOCUMENT_ROOT . "getdata/helpers.php";

$fileName = DOCUMENT_ROOT . "resources/heroes.json";

$fp = file_get_contents( $fileName );

$jsonDecodedArray = json_decode($fp,true);

foreach ( $jsonDecodedArray as $element ){
	$portraitUrl = $element['portraitUrl'];
	echo "Downloading $portraitUrl ... \n";
	shell_exec("./download_an_image.sh $portraitUrl");
}

