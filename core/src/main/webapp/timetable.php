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

require "script/php/init_timetable.php";

//Timetable extracted from official timetable with DOM parsing

//TODO mark next train if date is today and position it to center of screen
//TODO show calculated delay
//TODO show train number with link to new train details (including vonatosszeallitas?) instead of the official one
//TODO icon for '->'
?>
<!DOCTYPE html>
<html lang="hu">
<head>
<title>Vonat Információ - Menetrend</title>

<base target="_self" href="<?php echo $htmlBaseHref;?>/" />

<meta charset="utf-8" />
<!-- <meta charset="iso-8859-2" /> -->

<meta http-equiv="Content-Style-Type" content="text/css" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />

<meta name="robots" content="noindex, nofollow" />

<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
<meta http-equiv="pragma" content="no-cache" />

<!--
  <link rel="icon" href="http://elvira.mav-start.hu/xslvzs/res/favicon.ico"></link>
  <link type="image/ico" rel="shortcut icon" href="http://elvira.mav-start.hu/xslvzs/res/favicon.ico"></link>
  -->

<link rel="stylesheet" type="text/css" href="style/css/timetable.css" />
<!-- <link rel="stylesheet" media="handheld" type="text/css" href="style/css/timetable.css"/> -->
</head>
<body onload="window.location.hash='highlightedTrip'">
    <div class="mainArea">
        <!-- New search link -->
        <a id="searchLink" name="searchLink" href="search.php" class="searchLink">Új keresés</a>
        <h1 id="mainTitle" name="mainTitle" class="mainTitle">
            <?php
            echo $tripCollection->userFromStation->stationName . " - ";
            //$firephp->log($tripCollection->userViaStation, "viaStation");
            if(isset($tripCollection->userViaStation) && isset($tripCollection->userViaStation->stationName) && strlen($tripCollection->userViaStation->stationName) > 0){
			echo $tripCollection->userViaStation->stationName . " - ";
		}
		echo $tripCollection->userToStation->stationName;
		?>
        </h1>
        <h2>
            <span class="tripDate"> <?php echo $tripCollection->tripDate->format("Y.m.d");?>
            </span> <span class="tripDay"> <?php
            echo utf8_encode(strftime("%A", $tripCollection->tripDate->getTimestamp()));
            ?>
            </span>
        </h2>
        <table id="tripTable" name="tripTable" border="1">
            <tr>
                <th class="trip">Út</th>
                <th class="trip" colspan="2">Indulás és Érkezési Idő</th>
                <th class="trip">Ár</th>
            </tr>
            <tr>
                <th class="trip">Útrész</th>
                <th class="trip">Hivatalos</th>
                <th class="trip">Valós</th>
                <th class="trip">Vonat</th>
            </tr>
            <?php if($lastFinishedTripIndex > 0){?>
            <tr id="pastTrips" name="pastTrips">
                <th class="trip" colspan="4">Befejezett Utak</th>
            </tr>
            <?php
            }
            $tripIndex = 0;
            foreach ($tripCollection->trips as $trip){
        		$tripIndex++;
        		$evenOrOddTripCssClass = ($tripIndex%2==0)?"evenTrip":"oddTrip";
        		$tripinfoLinkId = "tripinfoLink" . $tripIndex;
        		$fromStationLinkId =  "fromStationLink" . $tripIndex . "_1";
        		$toStationLinkId =  "toStationLink" . $tripIndex . "_1";
        		$trainLinkId =  "trainLink" . $tripIndex . "_1";
        		if($tripIndex == $highlightedTripIndex){
                    //echo "<tr><td colspan=\"4\" style=\"padding:0; border:0\"><hr/></td></tr>";
                    $evenOrOddTripCssClass .= ",highlightedTrip";
                }
                if($tripIndex == $lastFinishedTripIndex + 1 && $tripIndex < $firstUpcomingTripIndex){
            ?>
            <tr id="currentTrips" name="currentTrips">
                <th class="trip" colspan="4">Aktuális Utak</th>
            </tr>
            <?php
                } if($tripIndex == $firstUpcomingTripIndex && $firstUpcomingTripIndex > 1){
            ?>
            <tr id="futureTrips" name="futureTrips">
                <th class="trip" colspan="4">Hátralevő Utak</th>
            </tr>
            <?php
                }
                echo "<tr class=\"trip," . $evenOrOddTripCssClass . "\">";
            ?>
            <td class="tripIndex"><?php if($tripIndex==$highlightedTripIndex){?> <a name="highlightedTrip" /> <?php }?> <?php echo $tripIndex;?>.
                út - <a id="<?php echo $tripinfoLinkId;?>" name="<?php echo $tripinfoLinkId;?>"
                href="timetable.php?tripIndex=<?php echo $tripIndex;?>">Részletek</a> <!-- 	    	<form method="post" action="tripinfo.php"> -->
                <!--		        <input id="tripIndex" name="tripIndex" type="hidden" value="<?php echo $tripIndex?>" /> --> <!-- 		        <button class="tripInfoBtn" id="tripInfoBtn" name="tripInfoBtn" type="submit">Részletek</button> -->
                <!-- 	    	</form> --> <?php
                if(count($trip->tripChapters) == 1){
                echo "<br><a id=\"" . $fromStationLinkId . "\" name=\"" . $fromStationLinkId . "\" href=\"" . $trip->tripChapters[0]->userFromStation->officialLink . "\">" . $trip->tripChapters[0]->userFromStation->stationName .
                "</a> - <a id=\"" . $toStationLinkId . "\" name=\"" . $toStationLinkId . "\" href=\"" . $trip->tripChapters[0]->userToStation->officialLink . "\">" . $trip->tripChapters[0]->userToStation->stationName . "</a>";
            }//if exactly one trip chapter
            ?>
            </td>
            <td class="officialTripTime"><?php echo $trip->getBeginTime()->official->departure->format("H:i") . " - " . $trip->getEndTime()->official->arrival->format("H:i");?>
            </td>
            <td class="realTripTime"><?php
            if(	isset($trip->getBeginTime()->actual->departure)|| isset($trip->getBeginTime()->estimated->departure) ||
			isset($trip->getEndTime()->actual->arrival) || isset($trip->getEndTime()->estimated->arrival)){
	        if(isset($trip->getBeginTime()->actual->departure)){
				echo $trip->getBeginTime()->actual->departure->format("H:i") . " - ";
			} else if(isset($trip->getBeginTime()->estimated->departure)){
				echo $trip->getBeginTime()->estimated->departure->format("H:i") . " - ";
			} else {
				echo $trip->getBeginTime()->official->departure->format("H:i") . " - ";
			}

			if(isset($trip->getEndTime()->actual->arrival)){
				echo $trip->getEndTime()->actual->arrival->format("H:i");
			} else if(isset($trip->getEndTime()->estimated->arrival)){
				echo $trip->getEndTime()->estimated->arrival->format("H:i");
			} else {
				echo $trip->getEndTime()->official->arrival->format("H:i");
			}
		}//if any actual or estimated time is given
		?></td>
            <td class="tripPrice"><?php
            if(isset($trip->tickets->firstClass) && isset($trip->tickets->firstClass->price) && strlen(trim($trip->tickets->firstClass->price)) > 0){
					echo "<span class=\"price\">1. oszt.: " . $trip->tickets->firstClass->price . " " . $trip->tickets->firstClass->priceUnit . "</span><br>";
				}
				echo "2. oszt.: " . $trip->tickets->secondClass->price . " " . $trip->tickets->secondClass->priceUnit;
				if(count($trip->tripChapters) == 1){
?> <br>Vonat: <a id="<?php echo $trainLinkId;?>" name="<?php echo $trainLinkId;?>" class="trainLink"
                href="<?php echo $trip->tripChapters[0]->train->officialLink?>"><?php echo $trip->tripChapters[0]->train->trainNumber?> </a>
                <?php
                }//if exactly one trip chapter
                ?>
            </td>
            </tr>
            <?php
    if(count($trip->tripChapters) > 1){
        $tripChapterIndex = 0;
        foreach ($trip->tripChapters as $tripChapter){
            $tripChapterIndex++;
            $fromStationLinkId =  "fromStationLink" . $tripIndex . "_" . $tripChapterIndex;
            $toStationLinkId =  "toStationLink" . $tripIndex . "_" . $tripChapterIndex;
            $trainLinkId =  "trainLink" . $tripIndex . "_" . $tripChapterIndex;
            echo "<tr class=\"tripChapter," . $evenOrOddTripCssClass . "\">";
            ?>
            <td class="tripChapterStation"><span class="station"><a id="<?php echo $fromStationLinkId;?>"
                    name="<?php echo $fromStationLinkId;?>" href="<?php echo $tripChapter->userFromStation->officialLink?>"> <?php echo $tripChapter->userFromStation->stationName;?>
                </a> </span> - <span class="station"><a id="<?php echo $toStationLinkId;?>" name="<?php echo $toStationLinkId;?>"
                    href="<?php echo $tripChapter->userToStation->officialLink?>"> <?php echo $tripChapter->userToStation->stationName;?>
                </a> </span>
            </td>
            <td class="officialTripChapterTime"><?php echo 
            $tripChapter->train->timetable[$tripChapter->userFromStation->stationName]->official->departure->format("H:i") . " - " .
            $tripChapter->train->timetable[$tripChapter->userToStation->stationName]->official->arrival->format("H:i");?></td>
            <td class="realTripChapterTime"><?php
            if(	isset($tripChapter->train->timetable[$tripChapter->userFromStation->stationName]->actual->departure) ||
    			isset($tripChapter->train->timetable[$tripChapter->userFromStation->stationName]->estimated->departure) ||
    			isset($tripChapter->train->timetable[$tripChapter->userToStation->stationName]->actual->arrival) ||
    			isset($tripChapter->train->timetable[$tripChapter->userToStation->stationName]->estimated->arrival)
    			){
    	       	if(isset($tripChapter->train->timetable[$tripChapter->userFromStation->stationName]->actual->departure)){
    	       		echo $tripChapter->train->timetable[$tripChapter->userFromStation->stationName]->actual->departure->format("H:i") . " - ";
    	       	} else if(isset($tripChapter->train->timetable[$tripChapter->userFromStation->stationName]->estimated->departure)){
    	       		echo $tripChapter->train->timetable[$tripChapter->userFromStation->stationName]->estimated->departure->format("H:i") . " - ";
    	       	} else{
    				echo $tripChapter->train->timetable[$tripChapter->userFromStation->stationName]->official->departure->format("H:i") . " - ";
    			}
    			if(isset($tripChapter->train->timetable[$tripChapter->userToStation->stationName]->actual->arrival)){
    	       		echo $tripChapter->train->timetable[$tripChapter->userToStation->stationName]->actual->arrival->format("H:i");
    	       	} else if(isset($tripChapter->train->timetable[$tripChapter->userToStation->stationName]->estimated->arrival)){
    	       		echo $tripChapter->train->timetable[$tripChapter->userToStation->stationName]->estimated->arrival->format("H:i");
    	       	} else{
    				echo $tripChapter->train->timetable[$tripChapter->userToStation->stationName]->official->arrival->format("H:i");
    			}
           	}//if any actual or estimated time is given
       	?>
            </td>
            <td class="train">
                <!--
                <?php echo "Vonat: " . $tripChapter->train->firstStation()->stationName . " - " . $tripChapter->train->lastStation()->stationName;?>
                -->
                Vonat: <a id="<?php echo $trainLinkId;?>" name="<?php echo $trainLinkId;?>" class="trainLink"
                href="<?php echo $tripChapter->train->officialLink?>"><?php echo $tripChapter->train->trainNumber?> </a>
            </td>
            </tr>
            <?php
        }//foreach tripChapter
    }//if more than one trip chapter
?>
            <?php
}//foreach trip
?>
        </table>
    </div>
</body>
</html>
