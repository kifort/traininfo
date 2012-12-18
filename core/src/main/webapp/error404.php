<?php
require "script/php/init.php";
$to = "tamas@kifor.hu";
$subject = "Error 404";
$message = "Requested URI: " . $_SERVER["REQUEST_URI"];
$message .= "\r\nHTTP Host: " . $_SERVER["HTTP_HOST"];
$message .= "\r\nServer name: " . $_SERVER["SERVER_NAME"];
$message .= "\r\nHttp referer: " . $_SERVER["HTTP_REFERER"];
$message .= "\r\nHTTP User Agent: " . $_SERVER["HTTP_USER_AGENT"];
$message .= "\r\nRemote address: " . $_SERVER["REMOTE_ADDR"];
$message .= "\r\nRemote host: " . $_SERVER["REMOTE_HOST"];
$from = "vonatinfo@vonatinfo.hu";
$headers = "From:" . $from;
mail($to,$subject,$message,$headers);
header( "Location: " . $htmlBaseHref );
?>