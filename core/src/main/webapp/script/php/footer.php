<?php
/**
 * Traininfo - Hungarian train timetable for Amazon Kindle eBook
 * @copyright Copyright (C) 2012-2022 Tamás Kifor
 *
 * This program is free software: you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any later
 * version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program. If not, see http://www.gnu.org/licenses/.
 *
 * If you have any question contact to Tamás Kifor via email: tamas@kifor.hu
 *
 * @author Tamás Kifor
 */
?>
<hr/>
<?php
//Test Kindle device and Apple WebKit browser
//$firephp->log($_SERVER["HTTP_USER_AGENT"], "HTTP_USER_AGENT");
$supportedClient = strpos($_SERVER["HTTP_USER_AGENT"], "AppleWebKit") && strpos($_SERVER["HTTP_USER_AGENT"], "Kindle");
if(!$supportedClient){
?>
<p>
	Ez a vonat információs weboldal Kindle elektronikus könyvekre lett kitalálva. Android-on javaslom a
	<a href="https://play.google.com/store/apps/details?id=app.mav.menetrend&hl=hu">Menetrend Droid</a>-ot vagy az
	<a href="https://play.google.com/store/apps/details?id=hu.porcica.mav.menetrend&hl=hu">AZ Menetrend - MÁV</a>-ot, iOS-en (iPhone, iPad, ...) az
	<a href="https://itunes.apple.com/app/id423649086">iMenetrend</a>-et, nem mobil eszközökre pedig ott a jó öreg
	<a href="http://elvira.mav-start.hu">ELVIRA</a>.<br/>
<p>
<?php
}//if not kindle
?>

Verzió: v<?php echo $appVersion;?><br>
Fejlesztő: <a href="http://tamas.kifor.hu">Kifor Tamás</a> 

<?php
//Test browser capabilities
//$firephp->log($_SESSION["screenWidth"], "screenWidth");
//$firephp->log($_SESSION["screenHeight"], "screenHeight");
//echo $_SESSION["screenWidth"] . " x ". $_SESSION["screenHeight"];

//$browser = get_browser(null, true); //browscap.ini not configured for get_browser function
//$firephp->log($browser, "browser");

// include_once "wurfl_config_xml.php"; //WURFL has couple of bugs :(
// $requestingDevice = $wurflManager->getDeviceForHttpRequest($_SERVER);
// $wurflInfo = $wurflManager->getWURFLInfo();
?>