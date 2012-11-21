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

//If trip details asked for
if(isset($_GET["tripIndex"])){
    $_SESSION["tripIndex"] = $_GET["tripIndex"];
    header( "Location: " . $htmlBaseHref . "/tripinfo.php" );
}

$tripCollection = $_SESSION["tripCollection"];

if(!isset($tripCollection)){
    header( "Location: " . $htmlBaseHref . "/search.php?errorMessage=" . urlencode("Sikertelen keresés") );
}

if(count($tripCollection->trips) == 0){
    header( "Location: " . $htmlBaseHref . "/search.php?errorMessage=" . urlencode("Sikertelen keresés") );
}


//Find highlighted trip
$lastFinishedTripIndex = 0;
$firstUpcomingTripIndex = count($tripCollection->trips) + 1;
$highlightedTripIndex = 1;

$tripDateYearsMonthsAndDays = explode(".", $tripCollection->tripDate->format("Y.m.d"));
$currentTime = new DateTime();
$tripIndex = 0;
foreach ($tripCollection->trips as $trip){
	   $tripIndex++;

	   //Begin of the trip
	   $tripBeginTime = $trip->getBeginTime()->official->departure;
	   if(isset($trip->getBeginTime()->actual->departure)){
	       $tripBeginTime = $trip->getBeginTime()->actual->departure;
	   } else if(isset($trip->getBeginTime()->estimated->departure)){
	       $tripBeginTime = $trip->getBeginTime()->estimated->departure;
	   }
	   $tripBeginIntervalFromNow = $currentTime->diff($tripBeginTime);
	   
	   //End of the trip
	   $tripEndTime = $trip->getEndTime()->official->arrival;
	   if(isset($trip->getEndTime()->actual->arrival)){
	       $tripEndTime = $trip->getEndTime()->actual->arrival;
	   } else if(isset($trip->getEndTime()->estimated->arrival)){
	       $tripEndTime = $trip->getEndTime()->estimated->arrival;
	   }
	   $tripEndIntervalFromNow = $currentTime->diff($tripEndTime);
	   
	   //Upate indexes
	   //If trip has already been finished
	   if($tripEndIntervalFromNow->format("%r%i")<=0){
	       $lastFinishedTripIndex = $tripIndex;
	   }
	   
	   //If trip has already been started
	   if($tripBeginIntervalFromNow->format("%r%i")>0){
	       $firstUpcomingTripIndex = $tripIndex;
	       $highlightedTripIndex = $tripIndex;
	       break;
	   }
}//foreach trip
    
if(isset($_SESSION["tripIndex"])){
    $highlightedTripIndex = $_SESSION["tripIndex"];
}

$firephp->log($lastFinishedTripIndex, "lastFinishedTripIndex");
$firephp->log($firstUpcomingTripIndex, "firstUpcomingTripIndex");
$firephp->log($highlightedTripIndex, "highlightedTripIndex");

// $tripSections = true;
// if($lastFinishedTripIndex == 0 && $firstUpcomingTripIndex > count($tripCollection->trips)){
//     $tripSections = false;
// }
?>