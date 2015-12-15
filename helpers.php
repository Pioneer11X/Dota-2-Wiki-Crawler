<?php

function get_content($URL,
                     $ajax_data=null,
                     $method=null,
                     $isjson=false,
                     $cookie=null,
                     $curlopt_header=1)
{
    $ch = curl_init();
    $userAgent = 'Mozilla/5.0 (Windows NT 6.2; WOW64)'.
    ' AppleWebKit/537.36 (KHTML, like Gecko) '.
    'Chrome/30.0.1599.17 Safari/537.36';
    curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
    curl_setopt($ch, CURLOPT_HEADER, $curlopt_header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,60);
    curl_setopt($ch,CURLOPT_TIMEOUT,120);
    curl_setopt($ch, CURLOPT_URL, $URL);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    if(isset($cookie))
    curl_setopt( $ch, CURLOPT_COOKIE, $cookie );

    if($method=="GET")
    {
        //echo $method;
        $header=array();
        $header[]="Accept-Language: en-us,en;q=0.5";
        $header[]="X-Requested-With: XMLHttpRequest";
        curl_setopt($ch,CURLOPT_HTTPGET,TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        //var_dump($header);
    }

    if(isset($ajax_data))
    {
        if($method=="POST")
        {
            $fields=$ajax_data['fields'];
            if(isset($ajax_data['cookie']))
                $cookie=$ajax_data['cookie'];
            if(isset($ajax_data['header']))
                $header=$ajax_data['header'];
              curl_setopt($ch,CURLOPT_POST, 1);
            if(isset($cookie))
                curl_setopt($ch,CURLOPT_COOKIE,$cookie);
            if(isset($header))
                curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
            if($isjson == true)
                curl_setopt($ch,CURLOPT_POSTFIELDS,
                            json_encode($fields));
            else
                curl_setopt($ch,CURLOPT_POSTFIELDS, $fields);
        }
    }
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

?>
