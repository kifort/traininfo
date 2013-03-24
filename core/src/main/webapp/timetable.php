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

//$firephp->log($tripCollection, "tripCollection");
?>
<!DOCTYPE html>
<html lang="hu">
<head>
<title>Vonat Információ - Menetrend</title>
<?php require "script/php/header.php";?>
<meta name="robots" content="noindex, nofollow" />
<link rel="stylesheet" type="text/css" href="style/css/timetable.css" />
<!-- <link rel="stylesheet" media="handheld" type="text/css" href="style/css/timetable.css"/> -->
</head>
<body onload="window.location.hash='highlightedTrip'">
    <div class="mainArea">
        <div class="navigationArea">
            <!-- Page history -->
            <span class="historyArea">
                <!-- New search link -->
                <a id="searchLink" name="searchLink" href="search.php" class="searchLink">Keresés</a>
                <span class="historySeparator">></span>
                <a id="refreshTripCollectionLink" name="refreshTripCollectionLink" href="search.php?refreshTripCollection" class="refreshTripCollectionLink"><?php echo $tripCollection->userToStation->stationName;?></a>
            </span>
            
            <span class="backtripLink">
                <!-- Backtrip link -->
                <a id="backtripLink" name="backtripLink" href="search.php?searchBack" class="backtripLink">Visszaút</a>
            </span>
        </div>
        
        <!-- Main title with most important stations (first, via and last) -->
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
        
        <!-- Trip date -->
        <h2>
            <span class="tripDate"> <?php echo $tripCollection->tripDate->format("Y.m.d");?></span>
            <span class="tripDay"> <?php echo utf8_encode(strftime("%A", $tripCollection->tripDate->getTimestamp()));?></span>
        </h2>
        
        <!-- Trip table -->
        <table id="tripTable" name="tripTable" border="1">
        
            <!-- Trip table header -->
            <tr>
                <th class="trip">Út</th>
                <th class="trip" colspan="2">Indulási és Érkezési Idő</th>
                <th class="trip">Ár</th>
            </tr>
            <tr>
                <th class="trip">Útrész</th>
                <th class="trip">Hivatalos</th>
                <th class="trip">Valós</th>
                <th class="trip">Késés</th>
            </tr>
            <?php if($lastFinishedTripIndex > 0){?>
            <tr id="pastTrips" name="pastTrips">
                <th class="trip" colspan="4">Befejezett Utak</th>
            </tr>
            
            
            <?php }?>
            
            <!-- Trip table body -->
            <?php
            $tripIndex = 0;
            foreach ($tripCollection->trips as $trip){
                //$firephp->log($trip, "trip");
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
            
            <!-- Current and upcoming trip separators -->
            <tr id="currentTrips" name="currentTrips">
                <th class="trip" colspan="4">Aktuális Utak</th>
            </tr>
            <?php
                } if($tripIndex == $firstUpcomingTripIndex && $firstUpcomingTripIndex > 1){
            ?>
            <tr id="futureTrips" name="futureTrips">
                <th class="trip" colspan="4">Hátralevő Utak</th>
            </tr>            
            <?php }?>
            
            
            <tr class="trip,<?php echo $evenOrOddTripCssClass;?>">
                <td class="tripIndex">
                <!-- Highlighted trip marker -->
                <?php if($tripIndex==$highlightedTripIndex){?>
                    <a name="highlightedTrip" />
                <?php }?>
                
                <!-- Trip index -->
                <?php echo $tripIndex . ".út - ";?>
                
                <!-- Trip details link -->
                <a id="<?php echo $tripinfoLinkId;?>" name="<?php echo $tripinfoLinkId;?>" href="timetable.php?tripIndex=<?php echo $tripIndex;?>">Részletek</a>
                    
                <!-- Trip chapter info if trip contains only one chapeter -->
                <?php if(count($trip->tripChapters) == 1){
                    echo "<br>
                    <a id=\"" . $fromStationLinkId . "\" name=\"" . $fromStationLinkId . "\" href=\"" . $trip->tripChapters[0]->userFromStation->officialLink . "\">" . 
                    $trip->tripChapters[0]->userFromStation->stationName . "</a> - 
                    <a id=\"" . $toStationLinkId . "\" name=\"" . $toStationLinkId . "\" href=\"" . $trip->tripChapters[0]->userToStation->officialLink . "\">" . 
                    $trip->tripChapters[0]->userToStation->stationName . "</a>";
                    if($trip->tripChapters[0]->train->otherInformation != null){
                        echo "<br>" . $trip->tripChapters[0]->train->otherInformation;
                    }
                }//if exactly one trip chapter
                ?>
                </td>
                
                <!-- Official trip times -->
                <td class="officialTripTime">
                    <?php echo $trip->getBeginTime()->official->departure->format("H:i") . " - " . $trip->getEndTime()->official->arrival->format("H:i");?>
                </td>
                
                <!-- Real trip times -->
                <td class="realTripTime">
                <?php
                //If any actual or estimated time is known 
                if(	isset($trip->getBeginTime()->actual->departure)|| isset($trip->getBeginTime()->estimated->departure) ||
    			    isset($trip->getEndTime()->actual->arrival) || isset($trip->getEndTime()->estimated->arrival)){
    	            
                    //Real departure if known
                    if(isset($trip->getBeginTime()->actual->departure)){
    				    echo $trip->getBeginTime()->actual->departure->format("H:i") . " - ";
    			    } else if(isset($trip->getBeginTime()->estimated->departure)){
    				    echo $trip->getBeginTime()->estimated->departure->format("H:i") . " - ";
    			    } else {
    				    echo $trip->getBeginTime()->official->departure->format("H:i") . " - ";
    			    }
    
    			    //Real arrival if known
        			if(isset($trip->getEndTime()->actual->arrival)){
        				echo $trip->getEndTime()->actual->arrival->format("H:i");
        			} else if(isset($trip->getEndTime()->estimated->arrival)){
        				echo $trip->getEndTime()->estimated->arrival->format("H:i");
        			} else {
        				echo $trip->getEndTime()->official->arrival->format("H:i");
        			}
    		    }//if any actual or estimated time is given
    		    ?>
    		    </td>
    		    
    		    <!-- Trip price and delay -->
                <td class="tripPrice">
                <?php
                //First class ticket
                if(isset($trip->tickets->firstClass) && isset($trip->tickets->firstClass->price) && strlen(trim($trip->tickets->firstClass->price)) > 0){
                    echo "<span class=\"price\">1. oszt.: " . $trip->tickets->firstClass->price . " " . $trip->tickets->firstClass->priceUnit . "</span><br>";
                }
                //Second class ticket
                echo "<span class=\"price\">2. oszt.: " . $trip->tickets->secondClass->price . " " . $trip->tickets->secondClass->priceUnit . "</span>";
    			
                //Delay
                $delay = $trip->getEndTime()->getDelay();
                if(isset($delay)){
                    echo "<br><span class=\"delay\">" . $delay->format("%i") . " perc késés</span>";
                }
                ?>
                </td>
            </tr>
            <?php
            //Separate row for trip chapters if trip contains more than one trip chapter 
            if(count($trip->tripChapters) > 1){
                $tripChapterIndex = 0;
                foreach ($trip->tripChapters as $tripChapter){
                    $tripChapterIndex++;
                    $fromStationLinkId =  "fromStationLink" . $tripIndex . "_" . $tripChapterIndex;
                    $toStationLinkId =  "toStationLink" . $tripIndex . "_" . $tripChapterIndex;
                    $trainLinkId =  "trainLink" . $tripIndex . "_" . $tripChapterIndex;
            ?>
            
            <!-- Trip chapter info -->
            <tr class="tripChapter,<?php echo $evenOrOddTripCssClass;?>">
                <!-- First and last staion of the trip chapter -->
                <td class="tripChapterStation">
                    <span class="station">
                        <a id="<?php echo $fromStationLinkId;?>" name="<?php echo $fromStationLinkId;?>" href="<?php echo $tripChapter->userFromStation->officialLink?>">
                            <?php echo $tripChapter->userFromStation->stationName;?>
                        </a>
                    </span> - 
                    <span class="station">
                        <a id="<?php echo $toStationLinkId;?>" name="<?php echo $toStationLinkId;?>" href="<?php echo $tripChapter->userToStation->officialLink?>">
                            <?php echo $tripChapter->userToStation->stationName;?>
                        </a>
                    </span>
                    <?php
                    if($tripChapter->train->otherInformation != null){
                        echo "<br>" . $tripChapter->train->otherInformation;
                    }
                    ?>
                </td>
                
                <!-- Official trip chapter times -->
                <td class="officialTripChapterTime">
                <?php echo 
                    $tripChapter->train->timetable[$tripChapter->userFromStation->stationName]->official->departure->format("H:i") . " - " .
                    $tripChapter->train->timetable[$tripChapter->userToStation->stationName]->official->arrival->format("H:i");
                ?>
                </td>
                
                <!-- Real trip chapter times -->
                <td class="realTripChapterTime">
                <?php
                //If any actual or estimated time is known
                if(	isset($tripChapter->train->timetable[$tripChapter->userFromStation->stationName]->actual->departure) ||
        			isset($tripChapter->train->timetable[$tripChapter->userFromStation->stationName]->estimated->departure) ||
        			isset($tripChapter->train->timetable[$tripChapter->userToStation->stationName]->actual->arrival) ||
        			isset($tripChapter->train->timetable[$tripChapter->userToStation->stationName]->estimated->arrival)
        			){

                    //Real departure if known
        	       	if(isset($tripChapter->train->timetable[$tripChapter->userFromStation->stationName]->actual->departure)){
        	       		echo $tripChapter->train->timetable[$tripChapter->userFromStation->stationName]->actual->departure->format("H:i") . " - ";
        	       	} else if(isset($tripChapter->train->timetable[$tripChapter->userFromStation->stationName]->estimated->departure)){
        	       		echo $tripChapter->train->timetable[$tripChapter->userFromStation->stationName]->estimated->departure->format("H:i") . " - ";
        	       	} else{
        				echo $tripChapter->train->timetable[$tripChapter->userFromStation->stationName]->official->departure->format("H:i") . " - ";
        			}
        			
        			//Real arrival if known
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
                
                <!-- Delay -->
                <td class="delay">
                    <?php
                    $delay = $tripChapter->train->timetable[$tripChapter->userToStation->stationName]->getDelay();
                    if(isset($delay)){
                        echo "<span class=\"delay\">" . $delay->format("%i") . " perc késés</span>";
                    }
                    ?>
                </td>
            </tr>
            <?php
            }//foreach tripChapter
        }//if more than one trip chapter
    }//foreach trip
?>
        </table>
    </div>
<?php include "script/php/footer.php";?>
</body>
</html>
