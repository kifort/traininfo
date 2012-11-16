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

require "script/php/init_search.php";
?>
<!DOCTYPE html>
<html lang="hu">
<head>
<title>Vonat Információ - Keresés</title>
<base target="_self" href="<?php echo $htmlBaseHref;?>/" />
<meta charset="utf-8" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta name="robots" content="noindex, nofollow" />

<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
<meta http-equiv="pragma" content="no-cache" />

<link rel="stylesheet" type="text/css" href="style/css/search.css"/>
<!-- <link rel="stylesheet" media="handheld" type="text/css" href="style/css/search.css"/> -->
<?php require "fragment/search_script_fragment.php";?>
</head>
<body>
    <h1>Vonat Információ</h1>
    <h2>Kindle e-könyv kiadás - v1.0</h2>
    <form method="post" action="search.php">
        <fieldset>
            <legend> Keresés </legend>
            <dl class="searchForm">
                <?php
                if(isset($errorMessages["search"])){
                    echo "<dt>" . $errorMessages["search"] . "</dt><dd></dd>";
                }
                ?>
                <dt>
                    <label for="fromStation">Honnan: </label>
                </dt>
                <dd>
                    <input id="fromStation" name="fromStation" type="text" role="textbox" placeholder="Honnan" maxlength="35"
                        autocomplete="off" aria-autocomplete="list" aria-haspopup="true" value="<?php echo $fromStation?>"></input>
                    <?php
                    if(isset($errorMessages["fromStation"])){
                        echo $errorMessages["fromStation"];
                    }
                    ?>
                </dd>
                <dt>
                    <label for="toStation">Hova:</label>
                </dt>
                <dd>
                    <input id="toStation" name="toStation" type="text" role="textbox" placeholder="Hova" maxlength="35" autocomplete="off"
                        aria-autocomplete="list" aria-haspopup="true" value="<?php echo $toStation?>"></input>
                    <?php
                    if(isset($errorMessages["toStation"])){
                        echo $errorMessages["toStation"];
                    }
                    ?>
                </dd>
                <dt>
                    <label for="viaStation">Érintve:</label>
                </dt>
                <dd>
                    <input id="viaStation" name="viaStation" type="text" role="textbox" placeholder="Érintve" maxlength="35"
                        autocomplete="off" aria-autocomplete="list" aria-haspopup="true" value="<?php echo $viaStation?>"></input>
                </dd>
                <dt>
                    <label for="when">Melyik napon:</label>
                </dt>
                <dd>
                    <input id="when" name="when" type="text" role="textbox" placeholder="Melyik napon" maxlength="35" autocomplete="off"
                        aria-autocomplete="list" aria-haspopup="true" value="<?php echo $when?>"></input>
                </dd>
                <!--
                <dt>
	            <label for="whatTime">Mikor:</label>
	            </dt>
                <dd>
	            <input id="whatTime" name="whatTime" type="text" role="textbox" value="Mostanában" placeholder="Mikor" maxlength="35" autocomplete="off" aria-autocomplete="list" aria-haspopup="true" value="<?php echo $whatTime?>"></input>
	            </dd>
	            -->
                <dt>
                    <label for="isFavourite">Kerüljön a kedvencek közé: </label>
                </dt>
                <dd>
                    <input id="isFavourite" name="isFavourite" type="checkbox" value="1"></input>
                </dd>
              </dl>
              <button id="searchBtn" name="searchBtn" type="submit" class="searchBtn">Menetrend</button>
        </fieldset>
    </form>
    <br>
<?php
    if(isset($_COOKIE[$favouriteCookieId])){
		$favourites = unserialize($_COOKIE[$favouriteCookieId]);
		if(count($favourites) > 0){
			echo '<form method="post" action="'.$htmlBaseHref.'/search.php">';
			echo '<fieldset><legend>Kedvencek</legend><dl class="favouriteForm">';
		
    		foreach($favourites as $favouriteTitle=>$key){
                echo '<dt><button id="favouriteBtn" name="favouriteBtn" type="submit" value="'.$favouriteTitle.'">'.$favouriteTitle.'</button></dt>';
                echo '<dd class="deleteFavouriteBtn"><button id="delete" name="deleteFavouriteBtn" type="submit" value="'.$favouriteTitle.'">Törlés</button></dd>';
  		    }//foreach $favouriteTitle
  		    echo '</dl></fieldset></form>';
		}//if favourite
	}//if favourite cookie
	
	//Test Kindle device and Apple WebKit browser
	$firephp->log($_SERVER["HTTP_USER_AGENT"], "HTTP_USER_AGENT");
	if(!strpos($_SERVER["HTTP_USER_AGENT"], "AppleWebKit") || !strpos($_SERVER["HTTP_USER_AGENT"], "Kindle")){
		echo "<br><hr/>
				<p>
				Ez a menetrend kereső Kindle elektronikus könyvekre lett kitalálva. Android-on javaslom a
				<a href=\"https://play.google.com/store/apps/details?id=app.mav.menetrend&hl=hu\">Menetrend Droid</a>-ot vagy az
				<a href=\"https://play.google.com/store/apps/details?id=hu.porcica.mav.menetrend&hl=hu\">AZ Menetrend - MÁV</a>-ot, iOS-en (iPhone, iPad, ...) az
				<a href=\"https://itunes.apple.com/app/id423649086\">iMenetrend</a>-et, nem mobil eszközökre pedig ott a jó öreg
				<a href=\"http://elvira.mav-start.hu\">ELVIRA</a>.<br/>
				<p>";
	}//else not kindle

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
</body>
</html>
