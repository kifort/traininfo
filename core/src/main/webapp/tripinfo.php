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

$tripCollection = $_SESSION["tripCollection"];
if(!isset($tripCollection)){
	header( "Location: " . $htmlBaseHref . "/search.php" );
}

if(!isset($_SESSION["tripIndex"])){
	header( "Location: " . $htmlBaseHref . "/timetable.php" );
}

$trip = $tripCollection->trips[$_SESSION["tripIndex"]-1];
if(isset($trip)){
	$_SESSION["trip"] = $trip;
}else{
	$trip = $_SESSION["trip"];
}
if(!isset($trip)){
	header( "Location: " . $htmlBaseHref . "/timetable.php" );
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
  <title>Vonat Információ - Menetrend</title>
  <?php require "script/php/header.php";?>
  <meta name="robots" content="noindex, nofollow" />
  <link rel="stylesheet" type="text/css" href="style/css/tripinfo.css"/>
  <!-- <link rel="stylesheet" media="handheld" type="text/css" href="style/css/tripinfo.css"/> -->
</head>
<body>
  <div class="mainArea">
    <a id="timetableLink" name="timetableLink" href="timetable.php" class="timetableLink">Vissza az utak listájához</a>
    <a id="searchLink" name="searchLink" href="search.php" class="searchLink">Új keresés</a>
    <h1 id="mainTitle" name="mainTitle" class="mainTitle"><?php
	    echo $tripCollection->userFromStation->stationName . " - ";
	    if(isset($tripCollection->userViaStation) && isset($tripCollection->userViaStation->stationName) && strlen($tripCollection->userViaStation->stationName) > 0){
			echo $tripCollection->userViaStation->stationName . " - ";
		}
		echo $tripCollection->userToStation->stationName;
	?></h1>
	<h2>
	  <span class="tripDate">
	    <?php echo $tripCollection->tripDate->format("Y.m.d");?>
	  </span>
	  <span class="tripTime">
	    <?php echo $trip->getBeginTime()->official->departure->format("H:i") . " - " . $trip->getEndTime()->official->arrival->format("H:i");?>
	  </span>
	</h2>
	<table border="1">
		<tr>
			<th class="trip"></th>
			<th class="trip">Állomás</th>
			<th class="trip">Indulás</th>
			<th class="trip">Érkezés</th>
			<th class="trip">Vonat</th>
		</tr>
    <?php
    	$stationIndex = 0;
    	$trainIndex = 0;
    	foreach ($trip->tripChapters as $tripChapter){
			$relevantStation = false;
			
			foreach ($tripChapter->train->stations as $station){
				if(!$relevantStation && $station->stationName==$tripChapter->userFromStation->stationName){
					$relevantStation = true;
				}
				if($relevantStation){
					$stationIndex++;
					$evenOrOddStationCssClass = ($stationIndex%2==0)?"evenStation":"oddStation";
					//$firephp->log($tripChapter->train->timetable, "Train timetable");
					if($station->stationName==$tripChapter->userFromStation->stationName){
						$officialArrival = "";
						$officialDeparture = $tripChapter->train->timetable[$station->stationName]->official->departure->format("H:i");
					}else{
						$officialArrival = $tripChapter->train->timetable[$station->stationName]->official->arrival->format("H:i");
						$officialDeparture = "";
					}
?>
					<tr class="station,<?php echo $evenOrOddStationCssClass;?>">
						<td class="trip.stationIndex"><?php echo $stationIndex . ".";?></td>
						<td class="trip.stationName"><a id="stationLink<?php echo $stationIndex;?>" name="stationLink<?php echo $stationIndex;?>" href="<?php echo $station->officialLink?>"><?php echo $station->stationName;?></a></td>
						<td class="trip.stationOfficalDeparture"><?php echo $officialDeparture?></td>
						<td class="trip.stationOfficalArrival"><?php echo $officialArrival;?></td>
						<td class="trip.train">
						<?php
							if($station->stationName==$tripChapter->userFromStation->stationName){
                                $trainIndex++;
                                $firephp->log($tripChapter->train, "tripChapter->train");
								echo $tripChapter->train->firstStation()->stationName . " - " . $tripChapter->train->lastStation()->stationName;
								echo " (<a id=\"trainLink" . $trainIndex . "\" name=\"trainLink" . $trainIndex. "\" class=\"trainLink\" href=\"" . $tripChapter->train->officialLink . "\">" . $tripChapter->train->trainNumber . "</a>)";
								if($tripChapter->train->otherInformation != null){
                                    echo "<br>" . $tripChapter->train->otherInformation;
                                }	
							}
						?>
						</td>
					</tr>
<?php
					if($station->stationName==$tripChapter->userToStation->stationName){
						$relevantStation = false;
					}
				}//if relevant station
			}//foreach station
		}//foreach tripChapter
?>
    </table>
  </div>
<?php include "script/php/footer.php";?>
</body>
</html>
    