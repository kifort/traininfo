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
require "script/php/init.php";
require "script/php/get_timetable.php";
require "script/php/show_timetable.php";

//If trip details asked for
if(isset($_GET["tripIndex"])){
  $_SESSION["tripIndex"] = $_GET["tripIndex"];
  header( "Location: " . $htmlBaseHref . "/tripinfo.php" );
}

$tripCollection = $_SESSION["tripCollection"];
if(!isset($tripCollection)){
	//$firephp->log("No trip collection in session");
	
    //Set search parameters in POST and SESSION 
	if(isset($_POST["searchBtn"])){
	    $_SESSION[search] = $_POST;
	} else if(isset($_SESSION[search])){
	    $_POST = $_SESSION[search];
	}
	else{
	    header( "Location: " . $htmlBaseHref . "/search.php?errorMessage=" . urlencode("Sikertelen keresés") );
	}

	//Check request parameters (set default values, and navigate to search if a mandatory parameter is missing)
	if(!isset($_POST["fromStation"])){
		header( "Location: " . $htmlBaseHref . "/search.php?errorMessage=" . urlencode("Induló állomás hiányzik") );
	}
	if(!isset($_POST["toStation"])){
		header( "Location: " . $htmlBaseHref . "/search.php?errorMessage=" . urlencode("Célállomás hiányzik") );
	}
	if(!isset($_POST["when"])){
		$_POST["when"] = "Ma";
	}
	if(!isset($_POST["when"])){
		$_POST["whatTime"] = "Egész nap";
	}

	//Store favourite in a cookie if the user asked for it
	if(isset($_POST["isFavourite"])){
		//$firephp->log("Favourite asked");
		
		//Get an array of favourites from cookie or create a new one if it is not stored yet
		if(isset($_COOKIE[$favouriteCookieId])){
			$favourites = unserialize($_COOKIE[$favouriteCookieId]);
		} else{
			$favourites = array();
		}
		
		//Create a unique title for current serach that is used as favourite key
		$favouriteTitle = $_POST["when"] . ": " . $_POST["fromStation"] . " - ";
		if(isset($_POST["viaStation"]) && strlen($_POST["viaStation"]) > 0){
			$favouriteTitle .= $_POST["viaStation"] . " - ";
		}
		$favouriteTitle .= $_POST["toStation"];
		$firephp->log("favourite: " . $favouriteTitle);
	
		if(!isset($favourites[$favouriteTitle])){
			//$firephp->log("set favourite: " . $favouriteTitle);
			$_POST["favouriteTitle"] = $favouriteTitle;
			$favourites[$favouriteTitle] = $_POST;
			//$firephp->log("update favourite cookie");
			$_COOKIE[$favouriteCookieId] = serialize($favourites);
			if(!setcookie($favouriteCookieId, serialize($favourites), strtotime( "+10 years" ), "/", "vonatinfo.kifor.hu")){
				//TODO error handling
				//$firephp->log("Unable to update favourite cookie");
			}
		}
	}//if asked to add a favourite

	//Parse the official timetable HTML with DOM and create an inner representation from it as described in TripCollectionClass.php
	$tripCollection = getTripCollection($_POST);
	$_SESSION["tripCollection"] = $tripCollection;
}//if already searched

if(!isset($tripCollection)){
	header( "Location: " . $htmlBaseHref . "/search.php?errorMessage=" . urlencode("Sikertelen keresés") );
}

if(count($tripCollection->trips) == 0){
	header( "Location: " . $htmlBaseHref . "/search.php?errorMessage=" . urlencode("Sikertelen keresés") );
}

//Timetable extracted from official timetable with DOM parsing
showTimetable($tripCollection);
?>
