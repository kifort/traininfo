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

$errorMessages = array();

//$firephp->log("Fire PHP log test");

//Test screen information existence in session
checkScreenInformation();

//Delete favourite if asked for
if(isset($_POST["deleteFavouriteBtn"])){
    deleteFavourite($favouriteCookieId);
}

$newSearch = false;

//Get favourite search parameters from session if asked for
if(isset($_POST["favouriteBtn"])){
    $favouriteSearch = getFavouriteSearch($favouriteCookieId);
    if($favouriteSearch){
        $_SESSION["search"] = $favouriteSearch;
        $newSearch = true;
    }
}

if(isset($_POST["searchBtn"])){
    //Check request parameters (set default values, and show error message if a mandatory parameter is missing)
    if ( validateSearch() ){
        $_SESSION["search"] = $_POST;
        $newSearch = true;

        //Store favourite in a cookie if the user asked for it
        if(isset($_POST["isFavourite"])){
            //$firephp->log("Favourite asked");
            addFavourite($favouriteCookieId);
        }
    }
}

if($newSearch){
    //Reset previous search result including selected trip if any
    resetSearch();

    //Parse the official timetable HTML with DOM and create an inner representation from it as described in TripCollectionClass.php
    $tripCollection = getTripCollection($_SESSION["search"]);
    $_SESSION["tripCollection"] = $tripCollection;

    if(!isset($tripCollection)){
        $errorMessages["search"] = "Sikertelen keresés";
    } else if(count($tripCollection->trips) == 0){
        $errorMessages["search"] = "Sikertelen keresés";
    } else{
        header( "Location: " . $htmlBaseHref . "/timetable.php" );
    }
} else{
    $when = "Ma";
    $whatTime = "Egész nap";
    if(isset($_SESSION["search"])){
        $fromStation = $_SESSION["search"]["fromStation"];
        $toStation = $_SESSION["search"]["toStation"];
        $viaStation = $_SESSION["search"]["viaStation"];
        if(isset($_SESSION["search"]["when"])){
            $when = $_SESSION["search"]["when"];
        }
        if(isset($_SESSION["search"]["whatTime"])){
            $whatTime = $_SESSION["search"]["whatTime"];
        }
    }//if search cached
}//else not new search

//Test screen information existence in session
function checkScreenInformation(){
    if(!isset($_SESSION["screenWidth"])) {
        header( "Location: " . $htmlBaseHref . "/index.php" );
    }
}

//Delete favourite if asked for
function deleteFavourite($favouriteCookieId){
    if(isset($_COOKIE[$favouriteCookieId])){
        $favourites = unserialize($_COOKIE[$favouriteCookieId]);
        $favouriteTitle = $_POST["deleteFavouriteBtn"];
        if(isset($favourites[$favouriteTitle])){
            unset($favourites[$favouriteTitle]);
            $_COOKIE[$favouriteCookieId] = serialize($favourites);
            if(!setcookie($favouriteCookieId, serialize($favourites), strtotime( "+10 years" ), "/", "vonatinfo.hu")){
                //$debugOut .= "Unable to update favourite cookie<br/>";
            }
        }
    }
}//deleteFavourite

function getFavouriteSearch($favouriteCookieId){
    $favourites = unserialize($_COOKIE[$favouriteCookieId]);
    $favouriteTitle = $_POST["favouriteBtn"];
    if(isset($favourites[$favouriteTitle])){
        return $favourites[$favouriteTitle];
    }
    return false;
}//getFavouriteSearch

//Check request parameters (set default values, and show error message if a mandatory parameter is missing)
function validateSearch(){
    $errorMessages = array();
    if(!isset($_POST["fromStation"])){
        $errorMessages["fromStation"] = "Kiinduló állomás hiányzik";
    }
    if(!isset($_POST["toStation"])){
        $errorMessages["toStation"] = "Célállomás hiányzik";
    }
    if(!isset($_POST["when"])){
        $_POST["when"] = "Ma";
    }
    if(!isset($_POST["when"])){
        $_POST["whatTime"] = "Egész nap";
    }
    if(count($errorMessages)>0){
        return false;
    } else{
        return true;
    }
}//validateSearch

//Store favourite in a cookie
function addFavourite($favouriteCookieId, $firephp){
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
    //$firephp->log("favourite: " . $favouriteTitle);

    if(!isset($favourites[$favouriteTitle])){
        //$firephp->log("set favourite: " . $favouriteTitle);
        $_POST["favouriteTitle"] = $favouriteTitle;
        $favourites[$favouriteTitle] = $_POST;
        //$firephp->log("update favourite cookie");
        $_COOKIE[$favouriteCookieId] = serialize($favourites);
        if(!setcookie($favouriteCookieId, serialize($favourites), strtotime( "+10 years" ), "/", "vonatinfo.hu")){
            //TODO error handling
            //$firephp->log("Unable to update favourite cookie");
        }
    }
}//addFavourite

//Reset previous search result including selected trip if any
function resetSearch(){
    //Reset previous search result if any
    if(isset($_SESSION["tripCollection"])){
        //$firephp->log("Trip collection in session. Delete it.");
        unset($_SESSION["tripCollection"]);
        $_SESSION["tripCollection"] = null;
    }

    //Reset previous selected trip if any
    if(isset($_SESSION["tripIndex"])){
        //$firephp->log("Trip index in session. Delete it.");
        unset($_SESSION["tripIndex"]);
        $_SESSION["tripIndex"] = null;
    }
}//resetSearch
?>